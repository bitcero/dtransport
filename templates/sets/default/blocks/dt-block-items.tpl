<{foreach item=item from=$block.downs}>
<{if $block.layout == 'vertical'}>
<div class="dt-block-item">
    <{if $item.image!=''}><a href="<{$item.link}>"><img src="<{$item.image}>" alt="<{$item.name}>" /></a><{/if}>
    <div class="info">
        <strong><a href="<{$item.link}>"><{$item.name}></a></strong>
        <{if $item.description!=''}>
        <span class="description"><{$item.description}></span>
        <{if $item.hits}><span class="data"><{$item.hits}></span><{/if}>
        <{if $item.urate}><span class="data"><{$block.lang_urate|replace:'%s':"<span class=urate>%s</span>"|replace:"%s":$item.urate}></span><{/if}>
        <{if $item.siterate}><span class="data"><{$item.siterate}></span><{/if}>
        <{if $item.author}><span class="data"><{$block.lang_author|replace:"%s":'<a href="%s"><strong>%s</strong></a>'|sprintf:$item.author.url:$item.author.name}></a></span><{/if}>
        <{if $block.showbutton}><a href="<{$item.link}>" class="btn btn-success btn-sm btn-down-item"><{cuIcon icon=svg-dtransport-download}> Download</a><{/if}>
        <{/if}>
    </div>
</div>
<{else}>
<div class="media dt-block-item">
    <{if $item.image != ''}>
    <div class="media-left">
        <a href="<{$item.link}>"><img src="<{$item.image}>" alt="<{$item.name}>" class="media-object"}></a>
    </div>
    <{/if}>
    <div class="media-body">
        <div class="info">
            <strong><a href="<{$item.link}>"><{$item.name}></a></strong>
            <{if $item.description!=''}>
            <span class="description"><{$item.description}></span>
            <{if $item.hits}><span class="data"><{$item.hits}></span><{/if}>
            <{if $item.urate}><span class="data"><{$block.lang_urate|replace:'%s':"<span class=urate>%s</span>"|replace:"%s":$item.urate}></span><{/if}>
            <{if $item.siterate}><span class="data"><{$item.siterate}></span><{/if}>
            <{if $item.author}><span class="data"><{$block.lang_author|replace:"%s":'<a href="%s"><strong>%s</strong></a>'|sprintf:$item.author.url:$item.author.name}></a></span><{/if}>
            <{if $block.showbutton}><a href="<{$item.link}>" class="btn btn-success btn-sm btn-down-item"><{cuIcon icon=svg-dtransport-download}> Download</a><{/if}>
            <{/if}>
        </div>
    </div>
</div>
<{/if}>



<{/foreach}>