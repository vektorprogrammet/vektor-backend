
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
