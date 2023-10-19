(function($) {

  /* globals jQuery, ajaxurl, wp */

  "use strict";

  var WpdsrDashboard = (function($) {

    var $dashboard = $('#wpdsr-dashboard'),

      $filters = $('.wpdsr-filters', $dashboard),
      $subheader = $('.wpdsr-filters-sticky', $dashboard),
      $menu = $('.wpdsr-aside .main-menu-block', $dashboard),
      $content = $('.wpdsr-filtered', $dashboard),

      $modal = $('.wpdsr-modal', $dashboard),
      $currentModal = false;

    var loading = true; // prevent some functions until window load

    /**
     * Filters
     */

    var filters = {

      startY: 0,
      topY: 0,
      bodyY: 0,
      width: 0,
      headerH: 0,
      dashboardMenuH: 0,
      $placeholder: $subheader.siblings('.wpdsr-filters-placeholder'),

      // filters.init()

      init: function() {

        if( window.location.hash.replace('#','') ){
          return false;
        }

        this.set();

        this.click( $('li:first', $filters) );

      },

      // filters.click()

      click: function($li) {

        var id = $li.data('id');

        if( $li.hasClass('active') ){
          return false;
        }

        $li.addClass('active')
          .siblings().removeClass('active');

        $('.wpdsr-row', $dashboard).removeClass('active');
        $('.wpdsr-row[data-group="'+ id +'"]', $content).addClass('active');

        // main menu

        menu.active(id);

        // scroll

        $('html, body').animate({
          scrollTop: 0
        }, 300);

      },

      // filters.set()

      set: function(){

        // this.topY = $content.offset().top;
        this.bodyY = $('#wpbody').offset().top;

        this.width = $dashboard.innerWidth();
        this.headerH = $subheader.outerHeight();

        this.startY = $('.wpdsr-header',$dashboard).offset().top + $('.wpdsr-header',$dashboard).outerHeight() - this.bodyY;

        this.$placeholder.height( this.headerH );

      },

      // filters.sticky()

      sticky: function(){

        var windowY = $(window).scrollTop();

        if( windowY >= this.startY ){

          this.$placeholder.show(0);
          $subheader.addClass('sticky').css({
            position: 'fixed',
            top: this.bodyY,
            width: this.width
          });

        } else {

          this.$placeholder.hide(0);
          $subheader.removeClass('sticky').css({
            position: 'static',
            top: 0,
            width: 'unset'
          });

        }

      },

    };

    /**
     * Menu
     */

    var menu = {

      // menu.active()

      active: function( id ){

        $('.navbar-item.active', $menu).find('li.navbar-item[data-id="'+ id +'"]').addClass('active')
          .siblings().removeClass('active');

      },

      // menu.clickSubmenu()

      clickSubmenu: function( $el ){

        var id = $el.attr('data-id'),
          activeFilter = '';

        if( ! id ){
          return;
        }

        activeFilter = $('li[data-id="'+ id +'"]');

        filters.click( activeFilter );

      },

    };

    /**
     * List
     */

    var list = {

      // list.switch()

      switch: function($el){

        var parent = $el.closest('.wpdsr-review-item');

        parent.toggleClass('wpdsr-inactive');

      }

    };

    /**
     * Forms
     */

    var forms = {

      // forms.generate()

      generate: function($el){

        if( $('.wpdsr-no-woocommerce').length ){
          alert('WooCommerce plugin is required but not activated');
          return;
        }

        $el.addClass('loading');

        window.onbeforeunload = null;

        var formData = $('#wpdsr-form-settings').serialize(); // Serialize the form data

        $.ajax( ajaxurl, {

          type : "POST",
          data : formData

        }).done(function(response){

          $el.removeClass('loading');

          if( response.success && response.data ){

            var html = '';

            $.each(response.data, function(index, value) {

              html += `
                <div class="wpdsr-review-item">

                  <input type="hidden" name="wpdsr[${index}][product_id]" value="${value.product_id}">

                  <div class="wpdsr-review-item-pic">
                    <img src="${value.product_image}" alt="" />
                  </div>

                  <div class="wpdsr-review-item-desc">

                    <h4 class="title">${value.product_title}</h4>

                    <textarea class="wpdsr-form-control wpdsr-form-textarea" rows="2" cols="50" name="wpdsr[${index}][review]">${value.review}</textarea>

                    <input class="wpdsr-form-control wpdsr-form-input wpdsr-form-el-small" type="text" name="wpdsr[${index}][author]" value="${value.author}">

                    <select class="wpdsr-form-control wpdsr-form-select wpdsr-form-el-small" name="wpdsr[${index}][rating]">
                      <option value="5" ${5 == value.rating ? 'selected' : ''}>5</option>
                      <option value="4" ${4 == value.rating ? 'selected' : ''}>4</option>
                      <option value="3" ${3 == value.rating ? 'selected' : ''}>3</option>
                      <option value="2" ${2 == value.rating ? 'selected' : ''}>2</option>
                      <option value="1" ${1 == value.rating ? 'selected' : ''}>1</option>
                    </select>

                    <input class="wpdsr-form-control wpdsr-form-input wpdsr-form-el-small" type="date" name="wpdsr[${index}][date]" value="${value.date}">

                    <div class="wpdsr-switcher" data-tooltip="Skip review">
                      <input class="wpdsr-switcher-input" type="checkbox" id="wpdsr-switcher-input-${index}" name="wpdsr[${index}][publish]" value="1" checked>
                      <label class="wpdsr-switcher-label" for="wpdsr-switcher-input-${index}"></label>
                    </div>

                  </div>
                </div>
              `;

            });

            $('.wpdsr-reviews-list').html( html );

          }

          $dashboard.attr('data-step','list');

        });

      },

      // forms.back()

      back: function($el){

        $dashboard.attr('data-step','form');

      },

      // forms.saveMultiple()

      saveMultiple: function($el){

        $el.addClass('loading');

        window.onbeforeunload = null;

        var formData = $('#wpdsr-form-list').serialize(); // Serialize the form data

        $.ajax( ajaxurl, {

          type : "POST",
          data : formData

        }).done(function(response){

          $el.removeClass('loading');
          $dashboard.addClass('settings-saved');

          setTimeout(function(){
            $dashboard.removeClass('settings-saved');
            forms.back();
          },2500);

        });

      },

      // forms.saveSingle()

      saveSingle: function($el){

        if( $('.wpdsr-no-woocommerce').length ){
          alert('WooCommerce plugin is required but not activated');
          return;
        }

        var error = false;

        $('#wpdsr-form-single .wpdsr-form-control').each(function(){
          if( ! $(this).val().length ){
            $(this).addClass('error');
            error = true;
          } else {
            $(this).removeClass('error');
          }
        });

        if( error ){
          return;
        }

        $el.addClass('loading');

        window.onbeforeunload = null;

        var formData = $('#wpdsr-form-single').serialize(); // Serialize the form data

        $.ajax( ajaxurl, {

          type : "POST",
          data : formData

        }).done(function(response){

          $el.removeClass('loading');
          $dashboard.addClass('settings-saved');

          setTimeout(function(){
            $dashboard.removeClass('settings-saved');
          },2500);

        });

      },

      // forms.saveSettings()

      saveSettings: function($el){

        var error = false;

        $('#wpdsr-form-settings .wpdsr-form-control').each(function(){
          if( ! $(this).val().length ){
            $(this).addClass('error');
            error = true;
          } else {
            $(this).removeClass('error');
          }
        });

        if( error ){
          return;
        }

        $el.addClass('loading');

        window.onbeforeunload = null;

        var formData = $('#wpdsr-form-settings').serialize(); // Serialize the form data

        $.ajax( ajaxurl, {

          type : "POST",
          data : formData

        }).done(function(response){

          $el.removeClass('loading');
          $dashboard.addClass('settings-saved');

          setTimeout(function(){
            $dashboard.removeClass('settings-saved');
          },2500);

        });

      },

    };

    /**
     * Validate
     */

    var validate = {

      // validate.number

      number: function(e){

        console.log(e);

      }

    };

    /**
     * Modal, icon select etc
     */

    var rowTabs = {

      // rowTabs.click()

      click: function($el){

        var $tabs = $el.closest('.wpdsr-row-tabs');

        var tab = $el.attr('data-tab');

        if( !tab || $el.hasClass('active') ){
          return;
        }

        $el.addClass('active')
          .siblings().removeClass('active');

        $tabs.find('.wpdsr-row-tab[data-tab="'+tab+'"]').addClass('active')
          .siblings().removeClass('active');

      }

    };

    /**
     * Modal, icon select etc
     */

    var modal = {

      // modal.open()

      open: function( $senderModal ){

        $currentModal = $senderModal;

        $currentModal.addClass('show');

        $('body').addClass('mfn-modal-open');

      },

      // modal.close()

      close: function(){

        if( ! $currentModal ){
          return false;
        }

        $currentModal.removeClass('show');

        $('body').removeClass('mfn-modal-open');

        $currentModal = false;

      }

    };

    /**
     * Cards hash navigation
     */

    var goToCard = function( el, e ){

      var locationURL = location.href.replace(/\/#.*|#.*/, ''),
        thisURL = el.href.replace(/\/#.*|#.*/, ''),
        hash = el.hash;

      if ( locationURL == thisURL ) {
        e.preventDefault();
      } else {
        return false;
      }

      menu.hash( hash );

    };

    /**
     * Select 2 init
     */

    var select2init = function(){

      // single review | saerch products

      if( $('.wpdsr-field-search-product .wpdsr-form-control').length ){
        $('.wpdsr-field-search-product .wpdsr-form-control').select2({
          multiple: true,
          ajax: {
            url: ajaxurl,
            dataType: 'json',
            type: "POST",
            quietMillis: 50,
            delay: 250,
            data: function (params) {
                return {
                  wpdsr_nonce: $('#wpdsr_nonce').val(),
                  action: 'wpdsr_product_search',
                  search: params.term
                };
              },
              processResults: function (data) {
                return {
                  results: data
                };
              },
              cache: true
          },
          escapeMarkup: function (markup) {
            return markup;
          },
          minimumInputLength: 3
        });
      }

    };

    /**
     * window.onbeforeunload
     * Warn user before leaving web page with unsaved changes
     */

    var enableBeforeUnload = function() {
      window.onbeforeunload = function(e) {
        return 'The changes you made will be lost if you navigate away from this page';
      };
    };

    /**
     * Bind on load
     */

    var bindOnLoad = function() {

      // onbeforeunload

      setTimeout(function(){
        $('#wpdsr-form-settings').on( 'change', '.wpdsr-form-control', function(){
          enableBeforeUnload();
        });
      },100);

    };

    /**
     * Bind
     */

    var bind = function() {

      // filters

      $filters.on( 'click', 'li', function(e){
        filters.click( $(this) );
      });

      // menu

      $menu.on( 'click', '.navbar-sub .navbar-item', function(e){
        menu.clickSubmenu( $(this) );
      });

      // row tabs

      $dashboard.on( 'click', '.wpdsr-row-tabs-nav li', function(e){
        rowTabs.click( $(this) );
      });

      // forms

      $dashboard.on( 'click', '.wpdsr-back', function(e){
        e.preventDefault();
        forms.back($(this));
      });

      $dashboard.on( 'click', '.wpdsr-generate', function(e){
        e.preventDefault();
        forms.generate($(this));
      });

      $dashboard.on( 'click', '.wpdsr-save-multiple', function(e){
        e.preventDefault();
        forms.saveMultiple($(this));
      });

      $dashboard.on( 'click', '.wpdsr-save-single', function(e){
        e.preventDefault();
        forms.saveSingle($(this));
      });

      $dashboard.on( 'click', '.wpdsr-save-settings', function(e){
        e.preventDefault();
        forms.saveSettings($(this));
      });

      $dashboard.on( 'change', '.wpdsr-switcher-input', function(e){
        e.preventDefault();
        list.switch($(this));
      });

      // validate

      $dashboard.on( 'keydown', 'input[type="number"]', function(e){
        validate.number(e);
      });

      // prevent Enter to submit form

      $(document).on( 'keydown', ':input:not(textarea)', function(e) {
        return e.key != 'Enter';
      });

      // window.scroll

      $(window).on('scroll', function() {

        filters.sticky();

      });

      // window resize

      $(window).on('debouncedresize', function() {

        filters.set();
        filters.sticky();

      });

    };

    /**
     * Ready
     * document.ready
     */

    var ready = function() {

      select2init();

      filters.init();

      bind();

    };

    /**
     * Load
     * window.load
     */

    var load = function() {

      loading = false;
      $dashboard.removeClass('loading');
      filters.hash();

      $(window).trigger('resize');

      bindOnLoad();

    };

    /**
     * Return
     */

    return {
      ready: ready,
      load: load
    };

  })(jQuery);

  /**
   * $(document).ready
   */

  $(function() {
    WpdsrDashboard.ready();
  });

  /**
   * $(window).load
   */

  $(window).on('load', function(){
    // WpdsrDashboard.load();
  });

})(jQuery);
