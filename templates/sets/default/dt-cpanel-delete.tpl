<div id="dt-cpanel-container">

    <{include file="db:dt-cp-header.tpl"}>

    <div class="row item-information">
        <div class="col-sm-5">

            <div class="alert alert-warning">
                <{$dtLang.confirmDelete}>
            </div>

            <div class="text-center">
            <form name="formDelete" action="<{$formAction}>" method="post">
                <button type="submit" class="btn btn-lg btn-warning">
                    <{$dtLang.imSure}>
                </button>
                <button type="button" class="btn btn-lg btn-default" onclick="history.go(-1);">
                    <{$dtLang.cancel}>
                </button>
            </form>
            </div>

        </div>
        <div class="col-sm-7">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><{$dtLang.itemInfo}></h3>
                </div>
                <div class="panel-body">
                    <div class="media">
                        <div class="media-left">
                            <a href="<{$item.link}>">
                                <img src="<{resize file=$item.image w=160 h=160}>" alt="<{$item.name}>" class="item-logo">
                            </a>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><a href="<{$item.link}>"><{$item.name}></a></h4>
                            <p>
                                <{$item.description}>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>