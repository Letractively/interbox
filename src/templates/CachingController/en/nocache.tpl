<p>Milliseconds used: {$elapsed}</p>
<a data-role="button" href="javascript:window.location.reload()">Test</a>
<div data-role="controlgroup" data-type="horizontal">
    <a data-role="button" href="#" data-theme="b">No Caching</a>
    <a data-role="button" href="?module=cache/timing">5s Timing</a>
    <a data-role="button" href="?module=cache/versioning&version={$version}">Versioning</a>
</div>