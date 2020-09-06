<{include file="db:dt-header.tpl"}>

<{if !$item.approved}>
<div class="dt-no-approved-msg alert alert-warning">
    <{$dtLang.noapproved}>
</div>
<{/if}>

<h1 class="dt-item-name">
    <{if $item.logo}>
    <img src="<{resize file=$item.logo w=100 h=100}>" alt="<{$item.name}>">
    <{/if}>
	<{$item.name}>
    <small><{$item.version}></small>
	<{if $item.new}>
		<sup><{$dtLang.new}></sup>
	<{elseif $item.updated}>
        <sup><{$dtLang.updated}></sup>
	<{/if}>
</h1>

<div class="dt-details-container">
    
    <!-- General Data -->
    <div class="dt-item-data">
        <a href="<{$item.download}>" class="dt-download-button">
            <{$dtLang.downnow}>
            <span class="icon"><span class="glyphicon glyphicon-download-alt"></span></span>
        </a>
        <div class="details">
            <ul class="dt-ratings">
                <li>
                    <div>
                        <div class="title"><{$dtLang.siterate}></div>
                        <div class="value">
                            <{$item.siterate}>
                        </div>
                        <div class="rate">
                            <{$item.localRating}>
                        </div>
                        <div class="data">
                            <{ourRate rate=$item.siterate}>
                        </div>
                    </div>
                </li>
                <li>
                    <div>
                        <div class="title"><{$dtLang.rateuser}></div>
                        <div class="value">
                            <{$item.rating}>
                        </div>
                        <div class="rate">
                            <{$item.usersRating}>
                        </div>
                        <div class="data">
                            <{$item.votes}>
                            <span class="glyphicon glyphicon-thumbs-up"></span>
                        </div>
                    </div>
                </li>
            </ul>
            
            <div class="all-data">
                
                <div class="rate-now">
                    <div class="title"><{$dtLang.yourrate}></div>
                    <form name="frmRating" id="frm-rating" method="post" action="">
                    <div class="dt-users-rating" id="dt-rating-thumbs">
                        <span style="width: 0%;"></span>
                    </div>
                    <select name="rate" id="dt-rates">
                        <{foreach item=r from=$ratings}>
                        <option value="<{$r}>"><{$r}></option>
                        <{/foreach}>
                    </select>
                    
                    <div id="dt-rating-legend"></div>
                    <input type="hidden" name="item" value="<{$item.id}>" />
                    <{$xoops_token}>
                    </form>
                    <div id="dt-rate-msgs"></div>
                </div>
                
                <ul>
                    <li>
                        <label><{$dtLang.version}></label>
                        <span><{$item.version}></span>
                    </li>
                    <li>
                        <label><{$dtLang.createdon}></label>
                        <span><{$item.created}></span>
                    </li>
                    <li>
                        <label><{$dtLang.langs}></label>
                        <span><{$item.langs}></span>
                    </li>
                    <li>
                        <label><{$dtLang.platforms}></label>
                        <span>
                            <{foreach item=os from=$item.platforms}>
                            <a href="<{$os.link}>"><{$os.name}></a><br>
                            <{/foreach}>
                        </span>
                    </li>
                    <li>
                        <label><{$dtLang.license}></label>
                        <span>
                            <{foreach item=lic from=$item.licenses}>
                            <a href="<{$lic.link}>"><{$lic.name}></a><br>
                            <{/foreach}>
                        </span>
                    </li>
                    <li>
                        <label><{$dtLang.published}></label>
                        <span>
                            <{foreach item=cat from=$item.categories key=i}>
                            <a href="<{$cat.link}>"><{$cat.name}></a>
                            <{/foreach}>
                        </span>
                    </li>
                    <li>
                        <label><{$dtLang.tags}></label>
                        <{dttags tags=$item.tags}>
                    </li>
                    <li>
                        <label><{$dtLang.author}></label>
                        <span class="author">
                            <a href="<{$item.author.url}>"><{$item.author.name}></a>
                        </span>
                    </li>
                </ul>

            </div>
            
        </div>
    </div>
    
    <div class="dt-item-content">

        <ul class="nav nav-tabs">
            <li role="presentation" class="active">
                <a href="#dt-item-description" aria-controls="dt-item-description" role="tab" data-toggle="tab"><{$dtLang.details}></a>
            </li>
            <{if $item.filegroups}>
                <li role="presentation">
                    <a href="#dt-item-options" aria-controls="dt-item-options" role="tab" data-toggle="tab"><{$dtLang.downopts}></a>
                </li>
            <{/if}>
            <li role="presentation">
                <a href="#dt-item-comments" aria-controls="dt-item-comments" role="tab" data-toggle="tab"><{$dtLang.comments}></a>
            </li>
        </ul>
        
        <div id="dt-item-details" class="tab-content">

            <div class="tab-pane fade in active" role="tabpanel" id="dt-item-description">
                <p class="lead excerpt"><{$item.shortdesc}></p>

                <div class="screenshot">
                    <a href="<{$item.image}>" rel="screenshot" class="item-images">
                        <img src="<{resize file=$item.image w=800}>" alt="<{$item.name}>" class="img-responsive">
                        <span class="zoom">
                            <{$dtLang.screensCount}>
                            <{cuIcon icon=svg-dtransport-zoom}>
                        </span>
                    </a>
                    <{foreach item=screen from=$item.screens}>
                        <a href="<{$screen.image}>" class="hidden item-images" rel="screenshot">#</a>
                    <{/foreach}>
                </div>

                <{$item.description}>

                <{if $item.features}>
                    <div class="dt-item-features" id="dt-item-features">
                        <h3><{$dtLang.features}></h3>
                    <{assign var=col value=1}>
                    <{foreach item=feat from=$item.features}>
                        <{if $col>=3}><{assign var=col value=1}><div class="clearfix"></div><{/if}>
                        <div class="dt-feature-item">
                            <a href="<{$feat.link}>" rel="dt-features" title="<{$feat.title}>"><{$feat.title}></a>
                        </div>
                        <{assign var="col" value=$col+1}>
                    <{/foreach}>
                    </div>

                    <div id="dt-features-loader">
                        <{cuIcon icon="svg-rmcommon-spinner-02"}>
                    </div>
                <{/if}>

                <{if $item.logs}>
                    <div class="dt-item-logs">
                        <h3><{$dtLang.changes}></h3>
                            <{foreach item=log from=$item.logs}>
                                <{$log.content}>
                            <{/foreach}>
                    </div>
                <{/if}>

            </div>

            <div id="dt-item-options" class="tab-pane fade" role="tabpanel">
                <p><{$dtLang.choose}></p>
                <hr>
                <{foreach item=group from=$item.filegroups}>
                    <{if $group.files}>
                        <h3 class="sl-htitles"><span class="glyphicon glyphicon-th-large"></span> <{$group.name}></h3>
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="text-left"><{$dtLang.title}></th>
                                <th class="text-center"><{$dtLang.date}></th>
                                <th class="text-center"><{$dtLang.hits}></th>
                                <th class="text-center"><{$dtLang.size}></th>
                                <th class="text-center"><{$dtLang.download}></th>
                            </tr>
                            </thead>
                            <tbody>
                            <{foreach item=file from=$group.files}>
                                <tr class="text-center" valign="middle">
                                    <td class="text-left">
                                        <{if $file.default}>
                                            <span class="glyphicon glyphicon-star text-success"></span>
                                            <strong><a href="<{$file.link}>"><{$file.title}></a></strong>
                                        <{else}>
                                            <a href="<{$file.link}>"><{$file.title}></a>
                                        <{/if}>
                                    </td>
                                    <td class="text-center"><{$file.date}></td>
                                    <td class="text-center"><{$file.hits}></td>
                                    <td class="text-center"><{$file.size}></td>
                                    <td class="text-center">
                                        <a href="<{$file.link}>" class="btn btn-sm <{if $file.default}>btn-success<{else}>btn-default<{/if}>">
                                            <span class="glyphicon glyphicon-download"></span>
                                            <{$dtLang.download}></a>
                                    </td>
                                </tr>
                            <{/foreach}>
                            </tbody>
                        </table>
                    <{/if}>
                <{/foreach}>
            </div>

            <div id="dt-item-comments" class="tab-pane fade" role="tabpanel">
                <a name="comments"></a>
                <{include file="db:dt-comments.tpl"}>
                <{$comments_form}>
            </div>

        </div>

    </div>
    
</div>



<!-- Item Details -->

<!-- Descargas relacionadas -->
<{if $related_items}>
    <{include file="db:dt-related-items.tpl" items=$related_items}>
<{/if}>

<!-- Descargas del día -->
<{if $daily_items}>
    <{include file="db:dt-day-download.tpl" items=$daily_items}>
<{/if}>
