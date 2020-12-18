require([
    'jquery'
    ], function ($){
    $(
        '<div class="powered-by">' +
        '   <span>' +
        '      Menu powered by' +
        '      <a href="https://redchamps.com?utm_source=easy-config-menu-main-menu" target="_blank">redChamps</a>' +
        '   </span>' +
        '</div>'
    ).insertAfter("#menu-redchamps-easyconfigmenu-item > .submenu > .submenu-title");
});
