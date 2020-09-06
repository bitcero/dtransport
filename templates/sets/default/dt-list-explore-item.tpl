<ul class="dt-explore-list">
<{foreach item=item from=$items}>
    <li class="dt-item">
        <a href="<{$item.link}>">
            <div class="data">
                <div class="media">
                    <div class="media-left">
                        <{if $item.logo}>
                            <img src="<{resize file=$item.logo w=100 h=100}>" class="media-object" alt="<{$item.title}>">
                        <{else}>
                            <img src="<{resize file=$item.image w=100 h=100}>" class="media-object" alt="<{$item.title}>">
                        <{/if}>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><{$item.name}> <small><{$item.version}></small></h4>
                        <div class="languages"><{$item.language}></div>
                        <p><{$item.description}></p>
                    </div>
                </div>
            </div>
            <div class="item-chart">
                <div class="c100 p<{$item.percent}> small chart">
                    <div class="slice">
                        <div class="bar"></div>
                        <div class="fill"></div>
                    </div>
                    <span><{$item.usersRate}></span>
                </div>
                <span class="votes"><{$item.langVotes}></span>
                <span class="downs"><{$item.langDownloads}></span>
            </div>
        </a>
    </li>
<{/foreach}>
</ul>