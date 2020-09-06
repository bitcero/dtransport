<ul class="dt-standard-list">
<{foreach item=item from=$items}>
    <li class="dt-item">
        <div class="row">
            <div class="col-sm-8 col-md-9">
                <div class="item-categories"><{dtcategories categories=$item.categories in=0}></div>
                <h5 class="item-name">
                    <a href="<{$item.link}>"><{$item.name}></a>
                </h5>
                <p class="item-description">
                    <{$item.description}>
                </p>
                <div class="item-rates">
                    <{$item.siterate}>
                    <span class="item-comments">
                        <a href="<{$item.link}>">
                            <{cuIcon icon=svg-rmcommon-comment}>
                            <{$item.comments}>
                            </a>
                    </span>
                </div>
            </div>
            <div class="col-sm-4 col-md-3 item-right-column">
                <a href="<{$item.link}>" class="btn btn-success btn-download">
                    <span class="caption"><{$dtLang.download}></span>
                    <span class="icon"><{cuIcon icon=svg-dtransport-download}></span>
                </a>
                <span class="item-languages text-warning"><{$item.language}></span>
            </div>
        </div>
    </li>
<{/foreach}>
</ul>