<ul>
  <{foreach item=link from=$block.links}>
  <li><a class="menuMain" href="<{$link.address}>"<{if $link.newwindow == 1 }> target="_blank"<{/if}>><{$link.title}></a></li>
  <{/foreach}>
</ul>