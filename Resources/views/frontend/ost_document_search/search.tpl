{* file to extend *}
{extends file="parent:frontend/index/index.tpl"}

{* set our namespace *}
{namespace name="frontend/ost-document-search/index"}

{block name="frontend_index_body_classes"}
    {$smarty.block.parent}
    is--ctl-listing
{/block}

{* remove left sidebar *}
{block name='frontend_index_content_left'}{/block}

{* main content *}
{block name='frontend_index_content'}
    <div class="panel content block has--border is--rounded" style="margin-bottom: 10px;">
        <div class="panel ost-document-search--search" style="padding-bottom: 0;">
            <h2 class="panel--title is--underline">
                {s name='search-document-headline'}EINFACH DOKUMENTE FINDEN{/s}
            </h2>
            <div class="panel--body is--wide">
                <form method="post" action="{url controller='OstDocumentSearch' action='search'}"
                      class="panel ost-document-search--search-form" id="ost-document-search--search-form">
                    <input style="width: 70%;" type="text" name="searchTerm" value="{$searchTerm}"
                           placeholder="{s name='search-customer-placeholder' force}Volltext Suchbegriff...{/s}">
                    <button style="width:20%; float: right; text-align: center;"
                            class="btn is--primary">{s name='search-customer-button'}Dokumente finden{/s}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="6column-listing">
        <div class="content listing--content">
            <div class="listing--wrapper visible--xl visible--l visible--m visible--s visible--xs">
                <div class="infinite--actions"></div>
                <div class="listing--container">
                    <div class="listing">
                        <div class="6column-listing--listing">
                            {foreach $documents as $document}
                                <div class="product--box box--6column">
                                    <div class="box--content is--rounded">
                                        <div class="product--info">
                                            <a href="{url controller='OstDocumentSearch' action='doc' file_id=$document['file_id']}"
                                               class="product--image">
                                            <span class="image--element">
                                                <span class="image--media">
                                                  <img src="{url controller='OstDocumentSearch' action='thumb' file_id=$document['file_id']}">
                                                </span>
                                            </span>
                                            </a>
                                            <a href="{url controller='OstDocumentSearch' action='doc' file_id=$document['file_id']}"
                                               class="product--title">{$document['title']|escapeHtml}</a>
                                            <div class="product--price-info">
                                                {foreach $document['tags'] as $tag}
                                                    <div class="document-tag">{$tag['name']}</div>
                                                {/foreach}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
