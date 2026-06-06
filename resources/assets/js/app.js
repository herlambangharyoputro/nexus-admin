/* ============================================================
   NEXUS ADMIN — APP JS
   Dependency: jQuery 3.x

   Modules:
     1. Sidebar (collapse + mobile toggle)
     2. Toast system    → window.Toast.show(msg, type)
     3. Modal system    → window.Modal.open(id), window.Modal.close()
     4. Notification    → window.Notif.markRead(id), markAllRead()
     5. Table sort      → auto-init via [data-sort-table]
     ============================================================ */

(function ($) {
  'use strict';

  /* ──────────────────────────────────────────────────────────
     0. CSRF setup untuk Laravel (auto-inject ke semua AJAX)
     ────────────────────────────────────────────────────────── */
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  /* ──────────────────────────────────────────────────────────
     1. Sidebar (collapse desktop + slide mobile)
     ────────────────────────────────────────────────────────── */
  const Sidebar = (function () {
    const $sidebar = $('.sidebar');
    const $main    = $('.main');
    const $overlay = $('.sidebar-overlay');
    const MOBILE_BREAKPOINT = 768;

    function isMobile() {
      return window.innerWidth < MOBILE_BREAKPOINT;
    }

    function toggle() {
      if (isMobile()) {
        $sidebar.toggleClass('mobile-hidden');
        $overlay.toggleClass('show');
        $('body').toggleClass('overflow-hidden');
      } else {
        $sidebar.toggleClass('collapsed');
        $main.toggleClass('expanded');
      }
    }

    function closeMobile() {
      $sidebar.addClass('mobile-hidden');
      $overlay.removeClass('show');
      $('body').removeClass('overflow-hidden');
    }

    function init() {
      // Set initial mobile state
      if (isMobile()) {
        $sidebar.addClass('mobile-hidden');
        $main.addClass('no-margin');
      }

      // Toggle button
      $(document).on('click', '.topbar-toggle, [data-sidebar-toggle]', function (e) {
        e.preventDefault();
        toggle();
      });

      // Overlay click to close (mobile)
      $(document).on('click', '.sidebar-overlay', closeMobile);

      // Auto-close on nav click (mobile only)
      $(document).on('click', '.sidebar .nav-item', function () {
        if (isMobile()) closeMobile();
      });

      // Re-evaluate on resize
      $(window).on('resize', function () {
        if (!isMobile()) {
          $sidebar.removeClass('mobile-hidden');
          $overlay.removeClass('show');
          $('body').removeClass('overflow-hidden');
          $main.removeClass('no-margin');
        } else {
          $sidebar.removeClass('collapsed').addClass('mobile-hidden');
          $main.removeClass('expanded').addClass('no-margin');
        }
      });
    }

    return { init: init, toggle: toggle, closeMobile: closeMobile };
  })();

  /* ──────────────────────────────────────────────────────────
     2. Toast System
     Usage: Toast.show('Saved!', 'success' | 'error' | 'info')
     ────────────────────────────────────────────────────────── */
  const Toast = (function () {
    const CONTAINER_ID = 'toast-container';
    const DURATION = 3200;

    function ensureContainer() {
      let $container = $('#' + CONTAINER_ID);
      if ($container.length === 0) {
        $container = $('<div id="' + CONTAINER_ID + '" class="toast-container"></div>');
        $('body').append($container);
      }
      return $container;
    }

    function show(msg, type) {
      type = type || 'info';
      const $container = ensureContainer();
      const id = 'toast-' + Date.now();

      const $toast = $(
        '<div id="' + id + '" class="toast ' + type + '">' +
          '<span class="toast-msg">' + $('<div>').text(msg).html() + '</span>' +
          '<button class="toast-close" aria-label="Close">&times;</button>' +
        '</div>'
      );

      $container.append($toast);

      // Manual close
      $toast.find('.toast-close').on('click', function () { dismiss($toast); });

      // Auto dismiss
      setTimeout(function () { dismiss($toast); }, DURATION);
    }

    function dismiss($toast) {
      $toast.css({ opacity: 0, transform: 'translateY(10px)' });
      setTimeout(function () { $toast.remove(); }, 200);
    }

    return { show: show };
  })();

  /* ──────────────────────────────────────────────────────────
     3. Modal System
     Usage:
       Modal.open('modalId')
       Modal.close()
       <button data-modal-open="myModal">Open</button>
       <button data-modal-close>Close</button>
     ────────────────────────────────────────────────────────── */
  const Modal = (function () {
    function open(id) {
      const $modal = $('#' + id);
      if ($modal.length === 0) return;
      $modal.addClass('show');
      $('body').addClass('overflow-hidden');
    }

    function close() {
      $('.modal-overlay.show').removeClass('show');
      $('body').removeClass('overflow-hidden');
    }

    function init() {
      // Trigger via data attribute
      $(document).on('click', '[data-modal-open]', function (e) {
        e.preventDefault();
        open($(this).data('modal-open'));
      });

      // Close button
      $(document).on('click', '[data-modal-close]', function (e) {
        e.preventDefault();
        close();
      });

      // Backdrop click (klik area gelap di luar .modal menutup; klik dalam modal tidak)
      $(document).on('click', '.modal-overlay', function (e) {
        if (e.target === e.currentTarget) close();
      });

      // ESC to close
      $(document).on('keydown', function (e) {
        if (e.key === 'Escape') close();
      });
    }

    return { open: open, close: close, init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     4. Notification (mark read)
     Usage:
       Notif.markRead(id) – tandai satu notif
       Notif.markAllRead() – tandai semua
     ────────────────────────────────────────────────────────── */
  const Notif = (function () {
    function markRead(id) {
      $('[data-notif-id="' + id + '"]').removeClass('unread');
      updateBadge();
    }

    function markAllRead() {
      $('[data-notif-id]').removeClass('unread');
      updateBadge();
    }

    function updateBadge() {
      const count = $('[data-notif-id].unread').length;
      const $badge = $('[data-notif-badge]');
      if (count > 0) {
        $badge.text(count).show();
      } else {
        $badge.hide();
      }
    }

    function init() {
      $(document).on('click', '[data-notif-id]', function () {
        markRead($(this).data('notif-id'));
      });
      $(document).on('click', '[data-notif-mark-all]', function (e) {
        e.preventDefault();
        markAllRead();
      });
    }

    return { markRead: markRead, markAllRead: markAllRead, init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     5. Table Sort (client-side, simple)
     Usage:
       <table data-sort-table>
         <thead>
           <tr><th data-sort="id">ID</th> ... </tr>
     ────────────────────────────────────────────────────────── */
  const TableSort = (function () {
    function init() {
      $(document).on('click', '[data-sort-table] th[data-sort]', function () {
        const $th = $(this);
        const $table = $th.closest('table');
        const $tbody = $table.find('tbody');
        const index = $th.index();
        const key = $th.data('sort');
        const currentDir = $th.data('dir') || 'asc';
        const newDir = currentDir === 'asc' ? 'desc' : 'asc';

        // Reset all th dir
        $table.find('th[data-sort]').removeData('dir').removeClass('sort-asc sort-desc');
        $th.data('dir', newDir).addClass('sort-' + newDir);

        const rows = $tbody.find('tr').get();
        rows.sort(function (a, b) {
          const va = $(a).find('td').eq(index).text().trim();
          const vb = $(b).find('td').eq(index).text().trim();
          const numA = parseFloat(va);
          const numB = parseFloat(vb);
          const aIsNum = !isNaN(numA);
          const bIsNum = !isNaN(numB);
          let cmp;
          if (aIsNum && bIsNum) cmp = numA - numB;
          else cmp = va.localeCompare(vb);
          return newDir === 'asc' ? cmp : -cmp;
        });

        $tbody.empty().append(rows);
      });
    }
    return { init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     6. Topbar action handlers
     ────────────────────────────────────────────────────────── */
  const Topbar = (function () {
    function init() {
      // Notification button — TODO: implement notification panel
      $(document).on('click', '[data-action="notifications"]', function (e) {
        e.stopPropagation();
        Toast.show('Notification panel belum diimplementasi', 'info');
      });

      // Logout — confirm sebelum redirect
      $(document).on('click', '[data-action="logout"]', function (e) {
        e.preventDefault();
        const href = $(this).attr('href');
        if (confirm('Yakin ingin keluar?')) {
          // TODO: kalau auth pakai POST logout (Laravel default), 
          // ganti ini dengan form submit + CSRF token
          window.location.href = href;
        }
      });
    }
    return { init: init };
  })();

  /* ──────────────────────────────────────────────────────────
     7. User Menu Dropdown (avatar+name+role di topbar)
     ────────────────────────────────────────────────────────── */
  const UserMenu = (function () {
    function close() {
      $('[data-user-menu]').removeClass('open').attr('aria-expanded', 'false');
      $('[data-user-dropdown]').removeClass('show');
    }

    function toggle($menu) {
      const isOpen = $menu.hasClass('open');
      // Close all first (in case multiple menus exist)
      close();
      if (!isOpen) {
        $menu.addClass('open').attr('aria-expanded', 'true');
        $menu.find('[data-user-dropdown]').addClass('show');
      }
    }

    function init() {
      // Toggle on click
      $(document).on('click', '[data-user-menu]', function (e) {
        // Don't toggle when clicking a dropdown item
        if ($(e.target).closest('[data-user-dropdown]').length) return;
        e.stopPropagation();
        toggle($(this));
      });

      // Close on outside click
      $(document).on('click', function (e) {
        if (!$(e.target).closest('[data-user-menu]').length) {
          close();
        }
      });

      // Close on ESC
      $(document).on('keydown', function (e) {
        if (e.key === 'Escape') close();
      });
    }

    return { init: init, close: close };
  })();

  /* ──────────────────────────────────────────────────────────
     Bootstrap on DOM ready
     ────────────────────────────────────────────────────────── */
  $(function () {
    Sidebar.init();
    Modal.init();
    Notif.init();
    TableSort.init();
    Topbar.init();
    UserMenu.init();
  });

  /* Expose to window for inline calls */
  window.Toast    = Toast;
  window.Modal    = Modal;
  window.Notif    = Notif;
  window.Sidebar  = Sidebar;
  window.UserMenu = UserMenu;

})(jQuery);