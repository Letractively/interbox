<form method="post" action="?module={$module}&function=save" data-ajax="false">
    <ul data-role="listview">
        <li data-role="fieldcontain">
            <label for="conf_key">Key</label>
            <input type="text" name="conf_key" id="conf_key"  />
        </li>
        <li data-role="fieldcontain">
            <label for="conf_value">Value</label>
            <input type="text" name="conf_value" id="conf_value"   />
        </li>
        <li>
            <fieldset class="ui-grid-a">
                <div class="ui-block-a"><button type="submit" data-theme="a">Add</button></div>
                <div class="ui-block-b"><a href="#" data-role="button" data-theme="d" data-rel="back">Cancel</a></div>
            </fieldset>
        </li>
    </ul>
</form>