<table class="blocks" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td id="mainmenu">
  <{foreach item=link from=$block.links}>
  <{if $link.parent == 0}>
    <a class="menuMain" href="<{$link.address}>"<{if $link.newwindow == 1 }> target="_blank"<{/if}>><{$link.title}></a>
	<{else}><{if  $link.parent == $link.currentParent}>
	<a class="menuSub" href="<{$link.address}>"<{if $link.newwindow == 1 }> target="_blank"<{/if}>><{$link.title}></a>
	     <{/if}><{/if}>
  	<{/foreach}>
    </td>
  </tr>
</table>