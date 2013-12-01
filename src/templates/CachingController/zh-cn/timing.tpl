<p>加载所使用毫秒：{$elapsed}</p>
<p>*每5秒钟，缓存的数据会被自动刷新，所以此时加载会慢许多。</p>
<a data-role="button" href="javascript:window.location.reload()">测试</a>
<a data-role="button" href="?module=cache&function=clear">清空缓存数据</a>
<div data-role="controlgroup" data-type="horizontal">
    <a data-role="button" href="?module=cache">无缓存</a>
    <a data-role="button" href="#" data-theme="b">5秒定时控制</a>
    <a data-role="button" href="?module=cache/versioning&version={$version}">版本控制</a>
</div>