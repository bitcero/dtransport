<{include file="db:dt-header.tpl"}>
Hola
<h1 class="dt-submit-title"><{$dtLang.submitTitle}></h1>

<!-- Progress indicator -->
<div class="dt-progress-global">

    <div class="title">
        <{$dtLang.overallProgress}>
    </div>

    <div class="dt-progress">

        <div class="progress">
            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 28%">
                <span class="sr-only">50% completed</span>
            </div>
        </div>

    <span class="point one done">
        1
        <em><{$dtLang.basic}></em>
    </span>

    <span class="point two done">
        2
        <em>Description</em>
    </span>

    <span class="point three">
        3
        <em><{$dtlang.details}></em>
    </span>

    <span class="point four">
        4
        <em>Clasification</em>
    </span>

    <span class="point five">
        5
        <em>Author</em>
    </span>

    </div>
</div>
<!--// End progress indicator -->

<!-- Submit form -->
<form name="submitForm" id="dt-submit-form" method="post" action="#">
    <div id="dt-submit-container">

        <h3 class="title"><{$dtLang.basicData}></h3>

        <div class="form-group">
            <label for="item-name"><{$dtLang.itemName}></label>
            <input type="text" id="item-name" class="form-control input-lg" placeholder="<{$dtLang.descriptiveName}>">
        </div>

        <div class="form-group">
            <label for="item-short"><{$dtLang.itemShort}></label>
            <textarea name="short" id="item-short" class="form-control" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="item-description"><{$dtLang.itemDescription}></label>
            <{$textEditor->render()}>
        </div>

        <div class="navigation">
            <a href="#" class="previous">
                <{$dtLang.previous}>
                <span class="arrow">
                    <{cuIcon icon=svg-rmcommon-caret-left}>
                </span>
            </a>

            <a class="next" href="#">
                <{$dtLang.next}>
                <span class="arrow">
                    <{cuIcon icon=svg-rmcommon-caret-right}>
                </span>
            </a>
        </div>
    </div>
</form>
<!--// End submit form -->
