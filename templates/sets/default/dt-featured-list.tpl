<div class="panel" id="dt-featured-downloads">
    <div class="panel-heading">
        <h3 class="panel-title">
            <{$dtLang.featured}>
        </h3>
    </div>
    <div class="panel-body">
        <{foreach item=item from=$featured_items}>
            <div class="featured-item">
                <div class="featured-image">
                    <a href="<{$item.link}>">
                        <{if $item.logo != ''}>
                            <img src="<{resize file=$item.logo w=160 h=160}>" alt="<{$item.name}>" class="media-object dt-image">
                        <{else}>
                            <img src="<{resize file=$item.image w=160 h=160}>" alt="<{$item.name}>" class="media-object dt-image">
                        <{/if}>
                    </a>
                </div>
                <div class="featured-content">
                    <div class="category"><{dtcategories categories=$item.categories}></div>
                    <div class="row">
                        <div class="col-sm-8">
                            <h4 class="featured-title"><a href="<{$item.link}>"><{$item.name}></a></h4>
                            <p class="featured-description"><{$item.description}></p>
                        </div>
                        <div class="col-sm-4 featured-right-column">
                            <a href="<{$item.link}>" class="btn btn-success btn-download">
                                <span class="caption"><{$dtLang.download}></span>
                                <span class="icon"><{cuIcon icon=svg-dtransport-download}></span>
                            </a>
                            <span class="featured-languages text-warning"><{$item.language}></span>
                        </div>
                    </div>
                </div>
            </div>
        <{/foreach}>
    </div>
</div>
