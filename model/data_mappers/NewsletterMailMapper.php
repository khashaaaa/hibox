<?php

OTBase::import('system.model.entities.NewsletterMailEntity');
OTBase::import('system.model.data_mappers.Mapper');
OTBase::import('system.lib.Validation.*');
OTBase::import('system.lib.Validation.Rules.*');

class NewsletterMailMapper extends Mapper
{
    protected $tableName = 'newsletters_mails';

    protected $entityType = 'NewsletterMailEntity';

    protected $columnsMap = array(
        'id' => 'id',
        'newsletterId' => 'newsletterId',
        'subscriberId' => 'subscriberId',
        'sent' => 'sent',
        'status' => 'status',
    );

    /**
     * @param NewsletterMailEntity $entity
     */
    protected function createNew($entity)
    {
        $newsletterId = intval($entity->getNewsletterId());
        $subscriberId = intval($entity->getSubscriberId());
        $status = $this->cms->escape($entity->getStatus());
        $this->cms->query('
                INSERT INTO `' . $this->tableName . '` SET
                    newsletterId="' . $newsletterId . '",
                    subscriberId="' . $subscriberId . '",
                    sent="' . date('Y-m-d H:i:s') . '",
                    status="' . $status . '"
                ', array($this->tableName));
    }

    /**
     * @param NewsletterMailEntity $entity
     */
    protected function update($entity)
    {
        $id = intval($entity->getId());
        $newsletterId = intval($entity->getNewsletterId());
        $subscriberId = intval($entity->getSubscriberId());
        $sent = $entity->getSent()->format('Y-m-d H:i:s');
        $status = $this->cms->escape($entity->getStatus());
        $this->cms->query('
                UPDATE `' . $this->tableName . '` SET
                    newsletterId="' . $newsletterId . '",
                    subscriberId="' . $subscriberId . '",
                    sent="' . $sent . '",
                    status="' . $status . '"
                WHERE 
                    id=' . $id
                , array($this->tableName));
    }

    /**
     * @return DateTime
     */
    public function findLastSentTime()
    {
        $date = $this->cms->querySingleValue('SELECT MAX(sent) FROM `' . $this->tableName . '`');
        if (empty($date)) {
            $date = time() - 24*60*60;
        } else {
            $date = strtotime($date);
        }
        return new DateTime(date('Y-m-d H:i:s', $date));
    }

    /**
     * Кол-во писем в очереди
     *
     * @param int $newsletterId
     * @return int
     */
    public function findNumberInQueue($newsletterId)
    {
        return $this->findMailsToSentCount($newsletterId) + $this->findMailsInProcessingCount($newsletterId);
    }

    /**
     * Кол-во писем ожидающих отправки
     *
     * @param int $newsletterId
     * @return int
     */
    public function findMailsToSentCount($newsletterId)
    {
        return $this->findMailsCount($newsletterId, 'WAIT');
    }

    /**
     * Кол-во писем отправляется в данный момент
     *
     * @param int $newsletterId
     * @return int
     */
    public function findMailsInProcessingCount($newsletterId)
    {
        return $this->findMailsCount($newsletterId, 'PROCESSING');
    }

    /**
     * Кол-во писем отправленных с ошибкой
     *
     * @param int $newsletterId
     * @return int
     */
    public function findMailsWithErrorCount($newsletterId)
    {
        $newsletterId = intval($newsletterId);

        $where = array();
        $where[] = 'newsletterId = ' . $newsletterId;
        $where[] = 'status LIKE "ERROR%"';

        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE ' . implode(' AND ', $where) . ' ';
        $result = $this->cms->queryMakeArray($sql, array($this->tableName));

        return count($result);
    }

    /**
     * Кол-во успешно отправленных писем
     *
     * @param int $newsletterId
     * @return int
     */
    public function findSentMailsCount($newsletterId)
    {
        return $this->findMailsCount($newsletterId, 'OK');
    }

    /**
     * Кол-во писем в рассылке с выбранным статусом
     *
     * @param int $newsletterId
     * @param string $status
     * @return int
     */
    private function findMailsCount($newsletterId, $status)
    {
        $newsletterId = intval($newsletterId);

        $where = array();
        $where[] = 'newsletterId = ' . $newsletterId;

        if (!empty($status)) {
            $where[] = 'status = "' . $status . '"';
        }

        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE ' . implode(' AND ', $where) . ' ';
        $result = $this->cms->queryMakeArray($sql, array($this->tableName));

        return count($result);
    }


    /**
     * Получить письма отправленные без ошибок
     *
     * @param $newsletterId
     * @param $limit
     * @return array
     */
    public function findMailsToSent($newsletterId, $limit)
    {
        $newsletterId = intval($newsletterId);
        $limit = intval($limit);
        $limit = $limit ? "LIMIT $limit" : "";

        $where = array();
        $where[] = 'newsletterId = ' . $newsletterId;
        $where[] = 'status = "WAIT"';

        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE ' . implode(' AND ', $where) . ' ' . $limit;
        $result = $this->cms->queryMakeArray($sql, array($this->tableName));

        return $this->createEntitiesFromData($result);
    }

    /**
     * Помечает письма статусом "В процессе отправки"
     *
     * @param array $ids
     */
    public function lockMails(array $ids)
    {
        if (!empty($ids)) {
            $where = 'WHERE id IN (' . implode(', ', $ids) . ')';
            $sql = 'UPDATE `' . $this->tableName . '` SET status="PROCESS" ' . $where;

            $this->cms->query($sql, array($this->tableName));
        }
    }

    /**
     * @param $newsletterId
     * @param SubscriberEntity[] $subscribers
     */
    public function initMails($newsletterId, $subscribers)
    {
        $valueFrames = $this->generateValues($newsletterId, $subscribers);
        foreach ($valueFrames as $valueFrame) {
            $sql = 'INSERT INTO `' . $this->tableName . '` (`newsletterId`, `subscriberId`, `sent`, `status`) VALUES ' . implode(',', $valueFrame);
            $this->cms->query($sql);
        }
    }

    /**
     * Сформировать фреймы для добавления
     *
     * @param $newsletterId
     * @param SubscriberEntity[] $subscribers
     * @param int $frameSize
     * @return array
     */
    private function generateValues($newsletterId, $subscribers, $frameSize = 100)
    {
        $itemFrames = array();
        $itemFrame = array();
        $itemCount = 0;

        foreach ($subscribers as $subscriber) {
            if ($itemCount < $frameSize) {
                // Добавить элемент к пакету
                $itemFrame[] = '(' . $newsletterId . ', ' . $subscriber->getId() . ', "' . date('Y-m-d H:i:s') . '", "WAIT")';
                $itemCount++;
            } else {
                // Создать новый пакет и добавить в него элемент
                $itemFrames[] = $itemFrame;
                $itemFrame = array();
                $itemFrame[] = '(' . $newsletterId . ', ' . $subscriber->getId() . ', "' . date('Y-m-d H:i:s') . '", "WAIT")';
                $itemCount = 1;
            }
        }
        // Добавить не полный пакет если он есть
        if (!empty($itemFrame)) {
            $itemFrames[] = $itemFrame;
        }

        return $itemFrames;
    }
}
