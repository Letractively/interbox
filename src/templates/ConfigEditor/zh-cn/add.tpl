<form method="post" action="?module={$module}&function=save" data-ajax="false">
    <ul data-role="listview">
        <li data-role="fieldcontain">
            <label for="conf_key">名称</label>
            <input type="text" name="conf_key" id="conf_key"  />
        </li>
        <li data-role="fieldcontain">
            <label for="conf_value">值</label>
            <input type="text" name="conf_value" id="conf_value"   />
        </li>
        <li>
            <fieldset class="ui-grid-a">
                <div class="ui-block-a"><button type="submit" data-theme="a">添加</button></div>
                <div class="ui-block-b"><a href="#" data-role="button" data-theme="d" data-rel="back">取消</a></div>
            </fieldset>
        </li>
    </ul>
</form>