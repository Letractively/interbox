<p>Milliseconds used: {$elapsed}</p>
<p>*Every 5 seconds, the cached data are automatically refreshed, and therefore, it becomes much slower.</p>
<a data-role="button" href="javascript:window.location.reload()">Test</a>
<a data-role="button" href="?module=cache&function=clear">Clear Cached Data</a>
<div data-role="controlgroup" data-type="horizontal">
    <a data-role="button" href="?module=cache">No Caching</a>
    <a data-role="button" href="#" data-theme="b">5s Timing</a>
    <a data-role="button" href="?module=cache/versioning&version={$version}">Versioning</a>
</div>