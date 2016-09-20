<div class="uk-grid uk-grid-divider uk-form uk-form-horizontal" data-uk-grid-margin>
    <div class="uk-width-medium-1-4">

        <div class="wk-panel-marginless">
            <ul class="uk-nav uk-nav-side" data-uk-switcher="{connect:'#nav-content'}">
                <li><a href="">Popover</a></li>
                <li><a href="">{{'Media' | trans}}</a></li>
                <li><a href="">{{'Content' | trans}}</a></li>
                <li><a href="">{{'General' | trans}}</a></li>
            </ul>
        </div>

    </div>
    <div class="uk-width-medium-3-4">

        <ul id="nav-content" class="uk-switcher">
            <li>

                <h3 class="wk-form-heading">{{'Popover' | trans}}</h3>

                <div class="uk-form-row">
                    <label class="uk-form-label">{{'Image' | trans}}</label>
                    <div class="uk-form-controls">
                        <field-media title="item.title" media="widget.data.image"></field-media>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label">{{'Popover Position' | trans}}</label>
                    <div class="uk-form-controls">
                        <select class="uk-form-width-medium" ng-model="widget.data['drop_position']">
                            <option value="left-center">Left</option>
                            <option value="right-center">Right</option>
                            <option value="bottom-center">Bottom</option>
                            <option value="top-center">Top</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label">{{'Popover Mode' | trans}}</label>
                    <div class="uk-form-controls">
                        <select class="uk-form-width-medium" ng-model="widget.data['drop_mode']">
                            <option value="hover">Hover</option>
                            <option value="click">Click</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" ng-model="widget.data['link_popover']"> {{'Link entire popover, if link exists' | trans}}</label>
                        </p>
                    </div>
                </div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'Media' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['media']"> {{'Show media' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label">{{'Image' | trans}}</label>
                    <div class="uk-form-controls">
                        <label><input class="uk-form-width-small" type="text" ng-model="widget.data['image_width']"> {{'Width (px)' | trans}}</label>
                        <p class="uk-form-controls-condensed">
                            <label><input class="uk-form-width-small" type="text" ng-model="widget.data['image_height']"> {{'Height (px)' | trans}}</label>
                        </p>
                    </div>
                </div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'Text' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" ng-model="widget.data['title']"> {{'Show title' | trans}}</label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" ng-model="widget.data['content']"> {{'Show content' | trans}}</label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-title-size">{{'Title Size' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-title-size" class="uk-form-width-medium" ng-model="widget.data['title_size']">
                            <option value="">Normal</option>
                            <option value="h1">H1</option>
                            <option value="h2">H2</option>
                            <option value="h3">H3</option>
                            <option value="h4">H4</option>
                            <option value="large">{{'Extra Large' | trans}}</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-content-size">{{'Content Size' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-content-size" class="uk-form-width-medium" ng-model="widget.data['content_size']">
                            <option value="">{{'Default' | trans}}</option>
                            <option value="large">{{'Text Large' | trans}}</option>
                            <option value="h2">H2</option>
                            <option value="h3">H3</option>
                            <option value="h4">H4</option>
                        </select>
                    </div>
                </div>

                <h3 class="wk-form-heading">{{'Link' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" ng-model="widget.data['link']"> {{'Show link' | trans}}</label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-link-style">{{'Style' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-link-style" class="uk-form-width-medium" ng-model="widget.data['link_style']">
                            <option value="text">{{'Text' | trans}}</option>
                            <option value="icon">{{'Icon Mini' | trans}}</option>
                            <option value="icon-small">{{'Icon Small' | trans}}</option>
                            <option value="icon-medium">{{'Icon Medium' | trans}}</option>
                            <option value="icon-large">{{'Icon Large' | trans}}</option>
                            <option value="icon-button">{{'Icon Button' | trans}}</option>
                            <option value="button">{{'Button' | trans}}</option>
                            <option value="primary">{{'Button Primary' | trans}}</option>
                            <option value="button-large">{{'Button Large' | trans}}</option>
                            <option value="primary-large">{{'Button Large Primary' | trans}}</option>
                            <option value="button-link">{{'Button Link' | trans}}</option>
                        </select>
                        <p class="uk-form-controls-condensed" ng-if="(['icon', 'icon-small', 'icon-medium', 'icon-large', 'icon-button'].indexOf(widget.data.link_style) > -1)">
                            <label>
                                <select class="uk-form-width-small" ng-model="widget.data['link_icon']">
                                    <option value="arrows-alt">{{'Arrows Alt' | trans}}</option>
                                    <option value="expand">{{'Expand' | trans}}</option>
                                    <option value="image">{{'Image' | trans}}</option>
                                    <option value="hand-o-right">{{'Hand' | trans}}</option>
                                    <option value="lightbulb-o">{{'Lightbulb' | trans}}</option>
                                    <option value="eye">{{'Eye' | trans}}</option>
                                    <option value="info">{{'Info' | trans}}</option>
                                    <option value="info-circle">{{'Info Circle' | trans}}</option>
                                    <option value="play-circle">{{'Play-circle' | trans}}</option>
                                    <option value="search">{{'Search' | trans}}</option>
                                    <option value="search-plus">{{'Search Plus' | trans}}</option>
                                    <option value="external-link">{{'External Link' | trans}}</option>
                                    <option value="external-link-square">External Link Square</option>
                                    <option value="angle-right">{{'Angle' | trans}}</option>
                                    <option value="angle-double-right" class="uk-icon-expand">{{'Angle Double' | trans}}</option>
                                    <option value="arrow-right">{{'Arrow' | trans}}</option>
                                    <option value="arrow-circle-right">{{'Arrow Circle' | trans}}</option>
                                    <option value="arrow-circle-o-right">Arrow Circle Outlined</option>
                                    <option value="long-arrow-right">{{'Long Arrow' | trans}}</option>
                                    <option value="caret-right">{{'Caret' | trans}}</option>
                                    <option value="caret-square-o-right">{{'Caret Square' | trans}}</option>
                                    <option value="chevron-right">{{'Chevron' | trans}}</option>
                                    <option value="chevron-circle-right">{{'Chevron Circle' | trans}}</option>
                                    <option value="plus">{{'Plus' | trans}}</option>
                                    <option value="plus-square">{{'Plus Square' | trans}}</option>
                                    <option value="plus-square-o">{{'Plus Square Outlined' | trans}}</option>
                                    <option value="plus-circle">{{'Plus Circle' | trans}}</option>
                                    <option value="share">{{'Share' | trans}}</option>
                                    <option value="share-square">{{'Share Square' | trans}}</option>
                                    <option value="share-square-o">{{'Share Square Outlined' | trans}}</option>
                                </select>
                                {{'Icon' | trans}}
                            </label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Text' | trans}}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <input type="text" ng-model="widget.data['link_text']">
                        </p>
                    </div>
                </div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'General' | trans}}</h3>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-class">{{'HTML Class' | trans}}</label>
                    <div class="uk-form-controls">
                        <input id="wk-class" class="uk-form-width-medium" type="text" ng-model="widget.data['class']">
                    </div>
                </div>

            </li>
        </ul>

    </div>
</div>
