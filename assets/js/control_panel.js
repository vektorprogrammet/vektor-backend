$('.c-sidebar-nav-link.active').each(function () {
  const link = $(this);
  link.closest('.c-sidebar-nav-dropdown').addClass('c-show');
});

$('.c-sidebar-nav-dropdown-toggle').each(function () {
  const toggle = $(this);
  toggle.click(function (e) {
      toggle.closest('.c-sidebar-nav-dropdown').toggleClass('c-show');
  });
});

function removeEmptyNavCategories() {
  const navTitles = $('.c-sidebar-nav-title');
  navTitles.each(function() {
    const title = $(this);
    if (title.prev().hasClass('c-sidebar-nav-title')) {
      title.prev().remove();
    }
  });
}

removeEmptyNavCategories();
