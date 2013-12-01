<p>Milliseconds used: {$elapsed}</p>
<p>*Once a newer version number is given, it refreshes its cached data, which takes longer correspondingly.</p>
<a data-role="button" href="javascript:window.location.reload()">Test</a>
<a data-role="button" href="?module=cache/versioning&version={$version}">New Version</a>
<a data-role="button" href="?module=cache&function=clear">Clear Cached Data</a>
<div data-role="controlgroup" data-type="horizontal">
    <a data-role="button" href="?module=cache">No Caching</a>
    <a data-role="button" href="?module=cache/timing">5s Timing</a>
    <a data-role="button" href="#" data-theme="b">Versioning</a>
</div>