<form method="post" action="?module={$module}&function=save" data-ajax="false">
    <input type="hidden" name="conf_key" value="{$text_key}"  />
    <ul data-role="listview">
        <li data-role="fieldcontain">
            <label for="conf_value">{$text_key}</label>
            <input type="text" name="conf_value" id="conf_value" value="{$text_value}"  />
        </li>
        <li>
            <fieldset class="ui-grid-a">
                <div class="ui-block-a"><button type="submit" data-theme="a">Save</button></div>
                <div class="ui-block-b"><a href="?module={$module}&function=delete&key={$urlparam_key}" data-role="button" data-theme="e">Delete</a></div>
            </fieldset>
        </li>
    </ul>
</form>