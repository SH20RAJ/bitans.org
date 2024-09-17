{strip}
  {if $system['css_customized']}
    <style type="text/css">
      {if $system['css_background']}
        body {
          background: {$system['css_background']};
        }

      {/if}

      {if $system['css_link_color']}
        a,
        .data-content .name a,
        .text-link,
        .post-stats-alt,
        .post-stats .fa,
        .side-nav>li.active>a,
        .navbar-container .data-content .name a {
          color: {$system['css_link_color']};
        }

      {/if}

      {if $system['css_btn_primary']}
        .btn-primary,
        .btn-primary:focus,
        .btn-primary:hover {
          background: {$system['css_btn_primary']}!important;
          border-color: {$system['css_btn_primary']}!important;
        }

      {/if}

      {if $system['css_header']}
        .main-header {
          background: {$system['css_header']};
        }

        .main-header .user-menu {
          border-left-color: {$system['css_header']};
        }

      {/if}

      {if $system['css_header_search']}
        .main-header .search-wrapper .form-control {
          background: {$system['css_header_search']};
        }

      {/if}

      {if $system['css_header_search_color']}
        .main-header .search-wrapper .form-control {
          color: {$system['css_header_search_color']};
        }

        .main-header .search-wrapper .form-control::placeholder {
          color: {$system['css_header_search_color']};
        }

        .main-header .search-wrapper .form-control::-webkit-input-placeholder {
          color: {$system['css_header_search_color']};
        }

        .main-header .search-wrapper .form-control:-moz-placeholder {
          color: {$system['css_header_search_color']};
          opacity: 1;
        }

        .main-header .search-wrapper .form-control:-ms-input-placeholder {
          color: {$system['css_header_search_color']};
        }

      {/if}

      {if $system['css_header_icons']}
        .header-icon,
        .header-icon * {
          color: {$system['css_header_icons']} ! important;
          fill: {$system['css_header_icons']} ! important;
        }

      {/if}

      {if $system['css_header_icons_night']}
        body.night-mode .header-icon,
        body.night-mode .header-icon * {
          color: {$system['css_header_icons_night']} ! important;
          fill: {$system['css_header_icons_night']} ! important;
        }

      {/if}

      {if $system['css_main_icons']}
        .main-icon,
        .main-icon *,
        .x-form-tools {
          color: {$system['css_main_icons']} ! important;
          fill: {$system['css_main_icons']} ! important;
        }

      {/if}

      {if $system['css_main_icons_night']}
        body.night-mode .main-icon,
        body.night-mode .main-icon *,
        body.night-mode .x-form-tools {
          color: {$system['css_main_icons_night']} ! important;
          fill: {$system['css_main_icons_night']} ! important;
        }

      {/if}

      {if $system['css_action_icons']}
        .action-icon,
        .action-icon * {
          color: {$system['css_action_icons']} ! important;
          fill: {$system['css_action_icons']} ! important;
        }

      {/if}

      {if $system['css_action_icons_night']}
        body.night-mode .action-icon,
        body.night-mode .action-icon * {
          color: {$system['css_action_icons_night']} ! important;
          fill: {$system['css_action_icons_night']} ! important;
        }

      {/if}

      {html_entity_decode($system['css_custome_css'], ENT_QUOTES)}
    </style>
  {/if}
{/strip}