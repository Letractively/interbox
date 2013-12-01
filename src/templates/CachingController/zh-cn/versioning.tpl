<p>加载所使用毫秒：{$elapsed}</p>
<p>*一旦有更新的版本号，缓存数据就会被刷新，加载变慢。</p>
<a data-role="button" href="javascript:window.location.reload()">测试</a>
<a data-role="button" href="?module=cache/versioning&version={$version}">更新版本号</a>
<a data-role="button" href="?module=cache&function=clear">清空缓存数据</a>
<div data-role="controlgroup" data-type="horizontal">
    <a data-role="button" href="?module=cache">无缓存</a>
    <a data-role="button" href="?module=cache/timing">5秒定时控制</a>
    <a data-role="button" href="#" data-theme="b">版本控制</a>
</div>