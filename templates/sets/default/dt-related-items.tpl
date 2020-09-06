<div class="panel dt-related">
    <div class="panel-heading">
        <h3 class="panel-title"><{$dtLang.related}></h3>
    </div>
    <div class="panel-body">

        <ul>
            <{foreach item=item from=$items}>

                <li class="dt-item related-item">
                    <div class="item-img">
                        <a href="<{$item.link}>">
                            <{if $item.logo != ''}>
                                <img src="<{resize file=$item.logo w=140 h=140}>" alt="<{$item.name}>">
                            <{else}>
                                <img src="<{resize file=$item.image w=140 h=140}>" alt="<{$item.name}>">
                            <{/if}>
                        </a>
                    </div>
                    <div class="item-description">
                        <h4><a href="<{$item.link}>"><{$item.name}></a></h4>
                        <p><{$item.description}></p>
                        <{$item.siterate}>
                    </div>
                    <div class="item-button">
                        <a href="<{$item.link}>" class="btn btn-info btn-download">
                            <span class="caption"><{$dtLang.download}></span>
                            <span class="icon"><{cuIcon icon=svg-dtransport-download}></span>
                        </a>
                    </div>
                </li>

            <{/foreach}>
        </ul>

    </div>
</div>