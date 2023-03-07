<tr style='border-top: 1px dotted #D3D3D3;'>
    <td>
        <input type='hidden' name='interval_id[]' value=''/>
        <table>
            <tr>
                <td><input type='radio' name='margin_type[][]' value='persent' checked='checked'/></td>
                <td>
                    <?=LangAdmin::get('margin_in_persent')?><br/>
                    <input type='text' name='margin[]' value=''/>
                </td>
            </tr>
            <tr>
                <td><input type='radio' name='margin_type[][]' value='fixed'/></td>
                <td>
                    <?=LangAdmin::get('fixed_margin')?><br/>
                    <input type='text' name='margin_fixed[]' value=''/>
                </td>
            </tr>
        </table>
    </td>
    <td>
        <input type='text' name='limit[]' value=''/>
    </td>
    <td>
        <input type='text' name='delivery[]' value=''/>
    </td>
    <td>
        <div class='delete_interval' style='cursor: pointer;' onClick='$(this).parent().parent().remove()'>
            <img src='templates/i/del.png' width='12' height='12' align='middle' style='vertical-align:middle'/>
        </div>
    </td>
</tr>
