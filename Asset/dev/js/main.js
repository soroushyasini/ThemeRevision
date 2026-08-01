(function (window, document, KB, $, hljs) {
    "use strict";

    var menuSequence = 0;

    function onReady(callback) {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", callback, { once: true });
        } else {
            callback();
        }
    }

    function enhancePage() {
        if (!document.body) {
            return;
        }

        document.body.classList.add("TR");
        enhanceLogo();
        enhanceBoardEditActions();
        enhanceProjectViewSwitcher();
        initMenu("section.sidebar-container > .sidebar");
        enhanceMetaMagik();

        if (hljs) {
            hljs.highlightAll();
        }
    }

    function enhanceLogo() {
        var logoLink = document.querySelector("header .logo > a");

        if (logoLink && !logoLink.querySelector("img")) {
            var image = document.createElement("img");
            image.src = getFavicon();
            image.alt = "";
            logoLink.textContent = "";
            logoLink.appendChild(image);
        }
    }

    function enhanceBoardEditActions() {
        var editActions = document.querySelectorAll(".task-board-expanded .task-board-header > a.js-modal-large");

        editActions.forEach(function (editAction) {
            if (editAction.getAttribute("aria-label")) {
                return;
            }

            var href = editAction.getAttribute("href");
            var menuLinks = editAction.parentNode.querySelectorAll(".dropdown li a");
            var matchingMenuLink = Array.prototype.find.call(menuLinks, function (menuLink) {
                return menuLink.getAttribute("href") === href;
            });

            if (!matchingMenuLink) {
                return;
            }

            var label = matchingMenuLink.textContent.trim();
            if (label) {
                editAction.setAttribute("aria-label", label);
                editAction.setAttribute("title", label);
            }
        });
    }

    function enhanceProjectViewSwitcher() {
        document.querySelectorAll(".project-header .views > li.active > a").forEach(function (activeView) {
            activeView.setAttribute("aria-current", "page");
        });
    }

    function enhanceMetaMagik() {
        var titles = document.querySelectorAll(".metamagik-footer-title");
        var values = document.querySelectorAll(".metamagik-footer-value");

        values.forEach(function (item, index) {
            var strong = titles[index] && titles[index].querySelector("strong");
            if (!strong) {
                return;
            }

            var text = strong.innerText.trim();
            item.title = text.replace(/[:：]\s*$/, "");
        });
    }

    function checkListSize($list) {
        return Boolean($list && $list.children(":not(.no-hover)").length > 25);
    }

    function addDropdownSearch($dropdownMenu) {
        if (!checkListSize($dropdownMenu) || $dropdownMenu.children("#dropdown-search").length) {
            return;
        }

        var $searchItem = $('<li id="dropdown-search" class="no-hover"></li>');
        var $searchInput = $('<input type="search" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">');

        $searchInput.attr({
            "aria-label": "Filter menu items",
            "placeholder": "Filter…"
        });
        $searchItem.append($searchInput);
        $dropdownMenu.prepend($searchItem);

        $searchInput.on("click", function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
        });

        $searchInput.on("input", function () {
            var keyword = String($(this).val() || "").trim().toLocaleLowerCase();
            var $items = $dropdownMenu.children("li:not(.no-hover):not(#dropdown-search)");

            $items.each(function () {
                var itemText = $(this).text().toLocaleLowerCase();
                $(this).toggle(!keyword || itemText.indexOf(keyword) !== -1);
            });
        });

        window.requestAnimationFrame(function () {
            $searchInput.trigger("focus");
        });
    }

    function initMenu(menuSelector) {
        var menu = document.querySelector(menuSelector);
        if (!menu || !menu.parentNode || menu.parentNode.querySelector(":scope > .themeRevisionMenuBtn")) {
            return;
        }

        var container = menu.parentNode;
        var button = document.createElement("button");
        var menuId = menu.id || "theme-revision-menu-" + (++menuSequence);

        menu.id = menuId;
        button.type = "button";
        button.className = "themeRevisionMenuBtn";
        button.setAttribute("aria-label", "Toggle navigation menu");
        button.setAttribute("aria-controls", menuId);
        button.setAttribute("aria-expanded", "false");
        button.innerHTML = '<span aria-hidden="true">&#9776;</span>';
        container.insertBefore(button, menu);

        function closeMenu(restoreFocus) {
            menu.classList.remove("tr-menu-open");
            button.setAttribute("aria-expanded", "false");
            if (restoreFocus) {
                button.focus();
            }
        }

        button.addEventListener("click", function (event) {
            event.stopPropagation();
            var willOpen = !menu.classList.contains("tr-menu-open");
            menu.classList.toggle("tr-menu-open", willOpen);
            button.setAttribute("aria-expanded", String(willOpen));
        });

        menu.addEventListener("click", function (event) {
            event.stopPropagation();
        });

        document.addEventListener("click", function () {
            closeMenu(false);
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && menu.classList.contains("tr-menu-open")) {
                closeMenu(true);
            }
        });
    }

    function getFavicon() {
        var icon = document.querySelector("head link[rel='icon']");
        return icon ? icon.getAttribute("href") : "/assets/img/favicon.png";
    }

    if (KB) {
        KB.on("modal.afterRender", function () {
            initMenu("#modal-overlay #modal-content section.sidebar-container > .sidebar");

            if (!$) {
                return;
            }

            if (checkListSize($("#form-action_name"))) {
                $("#form-action_name").select2();
            }

            if (checkListSize($("#form-owner_id"))) {
                $("#form-owner_id").select2();
                $(document)
                    .off("click.themeRevisionAssignMe", ".assign-me[data-target-id='form-owner_id']")
                    .on("click.themeRevisionAssignMe", ".assign-me[data-target-id='form-owner_id']", function () {
                        $("#form-owner_id").trigger("change");
                    });
            }
        });

        KB.on("dropdown.afterRender", function () {
            if (!$) {
                return;
            }

            var $dropdownMenu = $("#dropdown > ul.dropdown-submenu-open");
            $dropdownMenu.children("li:not(.no-hover)").has("i.fa").css({ fontSize: 0 });
            addDropdownSearch($dropdownMenu);
        });
    }

    onReady(enhancePage);
})(window, document, typeof KB === "undefined" ? null : KB, typeof jQuery === "undefined" ? null : jQuery, typeof hljs === "undefined" ? null : hljs);
