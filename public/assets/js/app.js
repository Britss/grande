document.addEventListener("click", function (event) {
    var toggle = event.target.closest("[data-password-toggle]");

    if (!toggle) {
        return;
    }

    var targetId = toggle.getAttribute("data-target");

    if (!targetId) {
        return;
    }

    var input = document.getElementById(targetId);

    if (!input) {
        return;
    }

    var isHidden = input.type === "password";

    input.type = isHidden ? "text" : "password";
    toggle.textContent = isHidden ? "Hide" : "Show";
    toggle.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");
});

document.addEventListener("DOMContentLoaded", function () {
    var notification = document.getElementById("customerOrderNotification");

    if (notification) {
        var closeButton = document.getElementById("customerOrderNotificationClose");
        var title = document.getElementById("customerOrderNotificationTitle");
        var text = document.getElementById("customerOrderNotificationText");
        var feed = document.querySelector("[data-customer-notification-feed]");
        var endpoint = notification.getAttribute("data-customer-notifications-url");
        var hideTimer = null;

        var hideNotification = function () {
            notification.classList.remove("show");
        };

        var formatNotificationTime = function (value) {
            if (!value) {
                return "";
            }

            var date = new Date(String(value).replace(" ", "T"));

            if (Number.isNaN(date.getTime())) {
                return "";
            }

            return date.toLocaleString([], {
                month: "short",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit"
            });
        };

        var renderFeed = function (items) {
            if (!feed || !Array.isArray(items) || items.length === 0) {
                return;
            }

            feed.innerHTML = "";

            items.forEach(function (item) {
                var row = document.createElement("div");
                var copy = document.createElement("div");
                var label = document.createElement("span");
                var message = document.createElement("p");
                var time = document.createElement("span");

                row.className = "customer-notification-feed__item";
                copy.className = "customer-notification-feed__copy";
                label.className = "customer-notification-feed__label";
                message.className = "customer-notification-feed__message";
                time.className = "customer-notification-feed__time";

                label.textContent = item.type === "reservation" ? "Reservation" : "Order";
                message.textContent = String(item.message || "Your account has a new status update.");
                time.textContent = formatNotificationTime(item.created_at);

                copy.appendChild(label);
                copy.appendChild(message);
                row.appendChild(copy);

                if (time.textContent) {
                    row.appendChild(time);
                }

                feed.appendChild(row);
            });
        };

        var showNotification = function (items) {
            if (!Array.isArray(items) || items.length === 0 || !title || !text) {
                return;
            }

            if (items.length === 1) {
                title.textContent = items[0].type === "reservation" ? "Reservation Updated" : "Order Updated";
                text.textContent = String(items[0].message || "Your account has a new status update.");
            } else {
                title.textContent = "Updates Available";
                text.textContent = "You have " + String(items.length) + " new account updates. Open your records to review the latest statuses.";
            }

            renderFeed(items);
            notification.classList.add("show");
            window.clearTimeout(hideTimer);
            hideTimer = window.setTimeout(hideNotification, 7000);
        };

        var fetchNotifications = function () {
            if (!endpoint || document.hidden) {
                return;
            }

            fetch(endpoint, {
                headers: {
                    "Accept": "application/json"
                }
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (!data || !data.success) {
                        return;
                    }

                    showNotification(data.notifications || []);
                })
                .catch(function () {});
        };

        if (closeButton) {
            closeButton.addEventListener("click", hideNotification);
        }

        fetchNotifications();
        window.addEventListener("focus", fetchNotifications);
        document.addEventListener("visibilitychange", fetchNotifications);
        window.setInterval(fetchNotifications, 30000);
    }
});

document.addEventListener("DOMContentLoaded", function () {
    var staffWorkspace = document.querySelector("[data-staff-order-poll-url]");

    if (!staffWorkspace || !window.fetch) {
        return;
    }

    var endpoint = staffWorkspace.getAttribute("data-staff-order-poll-url");
    var lastOrderId = parseInt(staffWorkspace.getAttribute("data-staff-order-last-id") || "0", 10) || 0;
    var isPolling = false;
    var toastTimer = null;

    var ensureToast = function () {
        var existing = document.getElementById("staffOrderToast");

        if (existing) {
            return existing;
        }

        var toast = document.createElement("div");
        var copy = document.createElement("div");
        var title = document.createElement("h3");
        var message = document.createElement("p");
        var actions = document.createElement("div");
        var paymentButton = document.createElement("button");
        var orderButton = document.createElement("button");
        var closeButton = document.createElement("button");

        toast.id = "staffOrderToast";
        toast.className = "staff-order-toast";
        toast.setAttribute("role", "status");
        toast.setAttribute("aria-live", "polite");
        copy.className = "staff-order-toast__copy";
        title.id = "staffOrderToastTitle";
        message.id = "staffOrderToastMessage";
        actions.className = "staff-order-toast__actions";
        paymentButton.type = "button";
        paymentButton.className = "button button-primary button-small";
        paymentButton.textContent = "Review Payments";
        paymentButton.setAttribute("data-dashboard-target", "payments");
        orderButton.type = "button";
        orderButton.className = "button button-secondary button-small";
        orderButton.textContent = "Manage Orders";
        orderButton.setAttribute("data-dashboard-target", "orders");
        closeButton.type = "button";
        closeButton.className = "staff-order-toast__close";
        closeButton.textContent = "Close";

        copy.appendChild(title);
        copy.appendChild(message);
        actions.appendChild(paymentButton);
        actions.appendChild(orderButton);
        actions.appendChild(closeButton);
        toast.appendChild(copy);
        toast.appendChild(actions);
        document.body.appendChild(toast);

        closeButton.addEventListener("click", function () {
            toast.classList.remove("show");
        });

        [paymentButton, orderButton].forEach(function (button) {
            button.addEventListener("click", function () {
                var target = button.getAttribute("data-dashboard-target");
                var dashboardButton = document.querySelector(".dashboard-workspace [data-dashboard-target='" + target + "']");

                if (dashboardButton) {
                    dashboardButton.click();
                }

                toast.classList.remove("show");
            });
        });

        return toast;
    };

    var showStaffOrderToast = function (payload) {
        var toast = ensureToast();
        var title = toast.querySelector("#staffOrderToastTitle");
        var message = toast.querySelector("#staffOrderToastMessage");
        var count = Number(payload.newOrderCount || 0);
        var paymentCount = Number(payload.pendingPaymentCount || 0);
        var fulfillmentCount = Number(payload.fulfillmentCount || 0);

        if (title) {
            title.textContent = count === 1 ? "New order received" : String(count) + " new orders received";
        }

        if (message) {
            message.textContent = String(paymentCount) + " waiting for payment review, " + String(fulfillmentCount) + " ready for fulfillment.";
        }

        toast.classList.add("show");
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(function () {
            toast.classList.remove("show");
        }, 9000);
    };

    var updateBadge = function (target, value) {
        var link = document.querySelector("[data-dashboard-target='" + target + "']");

        if (!link) {
            return;
        }

        var badge = link.querySelector(".dashboard-sidebar__badge");
        var count = Number(value || 0);

        if (count <= 0) {
            if (badge) {
                badge.remove();
            }
            return;
        }

        if (!badge) {
            badge = document.createElement("span");
            badge.className = "dashboard-sidebar__badge";
            link.appendChild(badge);
        }

        badge.textContent = String(count);
    };

    var pollStaffOrders = function () {
        if (!endpoint || isPolling || document.hidden) {
            return;
        }

        isPolling = true;

        fetch(endpoint + "?after_id=" + encodeURIComponent(String(lastOrderId)), {
            headers: {
                "Accept": "application/json"
            },
            credentials: "same-origin"
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (payload) {
                if (!payload || !payload.success) {
                    return;
                }

                if (Number(payload.latestOrderId || 0) > lastOrderId) {
                    lastOrderId = Number(payload.latestOrderId || lastOrderId);
                    staffWorkspace.setAttribute("data-staff-order-last-id", String(lastOrderId));
                }

                if (payload.paymentStats) {
                    updateBadge("payments", payload.paymentStats.pending_review);
                }

                if (payload.fulfillmentStats) {
                    updateBadge("orders", payload.fulfillmentStats.active_fulfillment);
                }

                if (Number(payload.newOrderCount || 0) > 0) {
                    showStaffOrderToast(payload);
                }
            })
            .catch(function () {})
            .finally(function () {
                isPolling = false;
            });
    };

    window.addEventListener("focus", pollStaffOrders);
    document.addEventListener("visibilitychange", pollStaffOrders);
    window.setInterval(pollStaffOrders, 20000);
});

document.addEventListener("DOMContentLoaded", function () {
    var widget = document.querySelector("[data-assistant-widget]");

    if (!widget) {
        return;
    }

    var config = window.GRANDE_ASSISTANT || {};
    var names = config.names || {};
    var websiteName = names.website || "GrandeGo";
    var shopName = names.shop || "Grande";
    var icons = config.icons || {};
    var links = config.links || {};
    var contact = config.contact || {};
    var toggle = widget.querySelector("[data-assistant-toggle]");
    var close = widget.querySelector("[data-assistant-close]");
    var windowEl = widget.querySelector("[data-assistant-window]");
    var body = widget.querySelector("[data-assistant-body]");
    var quickReplies = widget.querySelector("[data-assistant-quick-replies]");
    var form = widget.querySelector("[data-assistant-form]");
    var input = widget.querySelector("[data-assistant-input]");
    var badge = widget.querySelector("[data-assistant-badge]");
    var firstOpen = true;
    var typingTimer = null;

    if (!toggle || !close || !windowEl || !body || !quickReplies || !form || !input) {
        return;
    }

    var replyOptions = [
        { label: "Menu", text: "What is on the menu?", icon: "menu" },
        { label: "Hours", text: "What are your hours?", icon: "clock" },
        { label: "Reserve", text: "How do I make a reservation?", icon: "reserve" },
        { label: "Location", text: "Where are you located?", icon: "location" },
        { label: "Contact", text: "How can I contact you?", icon: "contact" },
        { label: "Order", text: "How do I place an order?", icon: "order" }
    ];

    var scrollToBottom = function () {
        body.scrollTop = body.scrollHeight;
    };

    var timeText = function () {
        return new Date().toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit"
        });
    };

    var appendIcon = function (parent, src, className) {
        if (!src) {
            return;
        }

        var image = document.createElement("img");
        image.src = src;
        image.alt = "";
        image.className = className;
        image.setAttribute("aria-hidden", "true");
        parent.appendChild(image);
    };

    var appendFormattedContent = function (parent, parts) {
        parts.forEach(function (part) {
            if (part.type === "text") {
                parent.appendChild(document.createTextNode(part.value));
                return;
            }

            if (part.type === "strong") {
                var strong = document.createElement("strong");
                strong.textContent = part.value;
                parent.appendChild(strong);
                return;
            }

            if (part.type === "link") {
                var link = document.createElement("a");
                link.href = part.href;
                link.textContent = part.label;
                parent.appendChild(link);
            }
        });
    };

    var addMessage = function (sender, parts) {
        var message = document.createElement("div");
        var bubbleWrap = document.createElement("div");
        var bubble = document.createElement("p");
        var timestamp = document.createElement("span");

        message.className = "assistant-message assistant-message--" + sender;
        bubbleWrap.className = "assistant-bubble-wrap";
        timestamp.className = "assistant-time";
        timestamp.textContent = timeText();

        if (sender === "bot") {
            var avatar = document.createElement("div");
            avatar.className = "assistant-avatar";
            appendIcon(avatar, icons.coffee, "assistant-avatar__icon");
            message.appendChild(avatar);
        }

        appendFormattedContent(bubble, Array.isArray(parts) ? parts : [{ type: "text", value: String(parts || "") }]);
        bubbleWrap.appendChild(bubble);
        bubbleWrap.appendChild(timestamp);
        message.appendChild(bubbleWrap);
        body.appendChild(message);
        scrollToBottom();
    };

    var showTyping = function () {
        var message = document.createElement("div");
        var avatar = document.createElement("div");
        var indicator = document.createElement("div");

        message.className = "assistant-message assistant-message--bot";
        message.setAttribute("data-assistant-typing", "true");
        avatar.className = "assistant-avatar";
        indicator.className = "assistant-typing";
        appendIcon(avatar, icons.coffee, "assistant-avatar__icon");

        for (var index = 0; index < 3; index += 1) {
            indicator.appendChild(document.createElement("span"));
        }

        message.appendChild(avatar);
        message.appendChild(indicator);
        body.appendChild(message);
        scrollToBottom();
    };

    var hideTyping = function () {
        var typing = body.querySelector("[data-assistant-typing]");

        if (typing) {
            typing.remove();
        }
    };

    var showQuickReplies = function () {
        quickReplies.innerHTML = "";

        replyOptions.forEach(function (reply) {
            var button = document.createElement("button");
            var label = document.createElement("span");

            button.type = "button";
            button.className = "assistant-reply";
            button.setAttribute("data-assistant-reply", reply.text);
            appendIcon(button, icons[reply.icon], "assistant-reply__icon");
            label.textContent = reply.label;
            button.appendChild(label);
            quickReplies.appendChild(button);
        });

        quickReplies.hidden = false;
        scrollToBottom();
    };

    var hideQuickReplies = function () {
        quickReplies.hidden = true;
        quickReplies.innerHTML = "";
    };

    var text = function (value) {
        return { type: "text", value: value };
    };

    var strong = function (value) {
        return { type: "strong", value: value };
    };

    var link = function (label, href) {
        return { type: "link", label: label, href: href || "#" };
    };

    var responseFor = function (message) {
        var normalized = String(message || "").toLowerCase();
        var phone = contact.phone || "+63 954 247 8073";
        var email = contact.email || "grande.pandesalcoffee.main@gmail.com";
        var address = contact.address || "Beside Puregold, in front of St. Anthony's Drug Store, Sindalan, San Fernando, Pampanga";

        if (/grandego|grande|website|site|coffee shop|shop name|brand name/.test(normalized)) {
            return [
                strong(websiteName),
                text(" is the website. "),
                strong(shopName),
                text(" is the coffee shop: Grande. Pan De Sal + Coffee.")
            ];
        }

        if (/menu|food|coffee|pandesal|pastry|pastries|sandwich|price|eat|drink/.test(normalized)) {
            return [
                text(shopName + " serves freshly baked Pan De Sal, premium coffee, pastries, and sandwiches. Browse the full list on the "),
                link("Menu page", links.menu),
                text(".")
            ];
        }

        if (/hour|open|close|time|247|24\/7|when/.test(normalized)) {
            return [text(shopName + " is open "), strong("24/7"), text(". Pan De Sal and coffee are available any time of day.")];
        }

        if (/reserv|book|table|seat/.test(normalized)) {
            return [
                text("Use the "),
                link("Reserve page", links.reserve),
                text(" to choose your date, time, guest count, and contact details. Logged-in customers can track reservations from the dashboard.")
            ];
        }

        if (/where|location|address|find|directions|sindalan|pampanga/.test(normalized)) {
            return [text(shopName + " is located at "), strong(address), text(". Drop by anytime.")];
        }

        if (/contact|call|phone|number|email|reach|feedback/.test(normalized)) {
            return [
                text("Reach us at "),
                strong(phone),
                text(" or "),
                strong(email),
                text(". You can also use the "),
                link("Feedback page", links.feedback),
                text(".")
            ];
        }

        if (/order|cart|checkout|buy|purchase|delivery|pick.?up|dine|gcash|cash/.test(normalized)) {
            return [
                text("Browse the "),
                link("Menu page", links.menu),
                text(", add items to your cart, then checkout through " + websiteName + " for dine-in or to-go orders from " + shopName + ". GCash receipt upload and cash payment options are supported.")
            ];
        }

        if (/hi|hello|hey|good morning|good afternoon|good evening|kumusta|kamusta/.test(normalized)) {
            return [text("Hello. I am the " + websiteName + " assistant for " + shopName + ". I can help with the menu, hours, reservations, ordering, location, and contact details.")];
        }

        if (/thank|thanks|salamat|ty/.test(normalized)) {
            return [text("You're welcome. Is there anything else I can help with?")];
        }

        if (/bye|goodbye|paalam|see you/.test(normalized)) {
            return [text("Goodbye. Visit " + websiteName + " anytime. " + shopName + " is open 24/7.")];
        }

        return [text("I am not sure about that yet. Try the quick buttons below, or contact " + shopName + " at "), strong(phone), text(".")];
    };

    var sendMessage = function (message) {
        var value = String(message || input.value || "").trim();

        if (!value) {
            return;
        }

        hideQuickReplies();
        addMessage("user", [text(value)]);
        input.value = "";
        showTyping();

        window.clearTimeout(typingTimer);
        typingTimer = window.setTimeout(function () {
            hideTyping();
            addMessage("bot", responseFor(value));
            showQuickReplies();
        }, 650);
    };

    var setOpen = function (isOpen) {
        widget.classList.toggle("is-open", isOpen);
        windowEl.hidden = !isOpen;
        windowEl.setAttribute("aria-hidden", isOpen ? "false" : "true");
        toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        toggle.setAttribute("aria-label", isOpen ? "Close " + websiteName + " assistant" : "Open " + websiteName + " assistant");

        if (isOpen) {
            if (firstOpen) {
                firstOpen = false;
                if (badge) {
                    badge.hidden = true;
                }
                window.setTimeout(function () {
                    addMessage("bot", [text("Hi. Welcome to " + websiteName + ".")]);
                }, 180);
                window.setTimeout(function () {
                    addMessage("bot", [text("I can help with " + shopName + " orders, reservations, hours, and contact details.")]);
                    showQuickReplies();
                }, 520);
            }
            window.setTimeout(function () {
                input.focus();
            }, 60);
        }
    };

    toggle.addEventListener("click", function () {
        setOpen(!widget.classList.contains("is-open"));
    });

    close.addEventListener("click", function () {
        setOpen(false);
        toggle.focus();
    });

    form.addEventListener("submit", function (event) {
        event.preventDefault();
        sendMessage();
    });

    quickReplies.addEventListener("click", function (event) {
        var button = event.target.closest("[data-assistant-reply]");

        if (!button) {
            return;
        }

        sendMessage(button.getAttribute("data-assistant-reply"));
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && widget.classList.contains("is-open")) {
            setOpen(false);
            toggle.focus();
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var menuFilter = document.querySelector("[data-menu-filter]");

    if (!menuFilter) {
        return;
    }

    var searchInput = menuFilter.querySelector("[data-menu-search]");
    var categoryButtons = menuFilter.querySelectorAll("[data-menu-category]");
    var items = document.querySelectorAll("[data-menu-item]");
    var categoryBlocks = document.querySelectorAll("[data-menu-category-block]");
    var emptyState = document.querySelector("[data-menu-empty]");
    var activeCategory = "all";

    var normalize = function (value) {
        return String(value || "").trim().toLowerCase();
    };

    var applyMenuFilter = function () {
        var query = normalize(searchInput ? searchInput.value : "");
        var shown = 0;

        items.forEach(function (item) {
            var text = normalize(item.getAttribute("data-menu-text"));
            var category = normalize(item.getAttribute("data-menu-category"));
            var matchesSearch = !query || text.indexOf(query) !== -1;
            var matchesCategory = activeCategory === "all" || category === activeCategory;
            var visible = matchesSearch && matchesCategory;

            item.hidden = !visible;

            if (visible) {
                shown += 1;
            }
        });

        categoryBlocks.forEach(function (block) {
            block.hidden = block.querySelectorAll("[data-menu-item]:not([hidden])").length === 0;
        });

        if (emptyState) {
            emptyState.hidden = shown !== 0;
        }
    };

    categoryButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            activeCategory = normalize(button.getAttribute("data-menu-category")) || "all";
            categoryButtons.forEach(function (categoryButton) {
                categoryButton.classList.toggle("is-active", categoryButton === button);
            });
            applyMenuFilter();
        });
    });

    if (searchInput) {
        searchInput.addEventListener("input", applyMenuFilter);
    }

    applyMenuFilter();
});

document.addEventListener("click", function (event) {
    var button = event.target.closest("[data-quantity-step]");

    if (!button) {
        return;
    }

    var control = button.closest(".menu-qty-control");
    var input = control ? control.querySelector('input[type="number"]') : null;

    if (!input) {
        return;
    }

    var step = Number(button.getAttribute("data-quantity-step")) || 0;
    var min = Number(input.getAttribute("min")) || 1;
    var max = Number(input.getAttribute("max")) || 20;
    var current = Number(input.value) || min;
    var next = Math.min(max, Math.max(min, current + step));

    input.value = String(next);
    input.dispatchEvent(new Event("change", { bubbles: true }));
});

document.addEventListener("DOMContentLoaded", function () {
    var revealItems = document.querySelectorAll("[data-reveal]");

    if (!revealItems.length) {
        return;
    }

    if (!("IntersectionObserver" in window)) {
        revealItems.forEach(function (item) {
            item.classList.add("is-visible");
        });
        return;
    }

    var revealObserver = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.2,
        rootMargin: "0px 0px -8% 0px"
    });

    revealItems.forEach(function (item) {
        revealObserver.observe(item);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var header = document.querySelector(".site-header");
    var toggle = document.querySelector("[data-nav-toggle]");
    var menu = document.querySelector("[data-nav-menu]");
    var mobileQuery = window.matchMedia("(max-width: 900px)");

    if (!header || !toggle || !menu) {
        return;
    }

    var setMenuState = function (isOpen) {
        header.classList.toggle("is-nav-open", isOpen);
        menu.classList.toggle("is-open", isOpen);
        menu.setAttribute("aria-hidden", mobileQuery.matches && !isOpen ? "true" : "false");
        toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
        toggle.setAttribute("aria-label", isOpen ? "Close navigation menu" : "Open navigation menu");
    };

    toggle.addEventListener("click", function (event) {
        event.stopPropagation();
        setMenuState(toggle.getAttribute("aria-expanded") !== "true");
    });

    menu.querySelectorAll("a").forEach(function (link) {
        link.addEventListener("click", function () {
            if (mobileQuery.matches) {
                setMenuState(false);
            }
        });
    });

    document.addEventListener("click", function (event) {
        if (!mobileQuery.matches || header.contains(event.target)) {
            return;
        }

        setMenuState(false);
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            setMenuState(false);
        }
    });

    var syncMenuState = function (query) {
        if (!query.matches) {
            setMenuState(false);
            return;
        }

        setMenuState(false);
    };

    setMenuState(false);

    if (typeof mobileQuery.addEventListener === "function") {
        mobileQuery.addEventListener("change", syncMenuState);
    } else if (typeof mobileQuery.addListener === "function") {
        mobileQuery.addListener(syncMenuState);
    }
});

document.addEventListener("DOMContentLoaded", function () {
    var sidebarLinks = document.querySelectorAll("[data-dashboard-target]");
    var dashboardPanels = document.querySelectorAll("[data-dashboard-panel]");

    if (!sidebarLinks.length || !dashboardPanels.length) {
        return;
    }

    var switchDashboardPanel = function (target, syncUrl) {
        if (!target) {
            return;
        }

        var hasTarget = Array.prototype.some.call(sidebarLinks, function (link) {
            return link.getAttribute("data-dashboard-target") === target;
        });

        if (!hasTarget) {
            return;
        }

        sidebarLinks.forEach(function (link) {
            link.classList.toggle("is-active", link.getAttribute("data-dashboard-target") === target);
        });

        dashboardPanels.forEach(function (panel) {
            panel.classList.toggle("is-active", panel.getAttribute("data-dashboard-panel") === target);
        });

        if (syncUrl !== false && window.location.pathname.indexOf("/dashboard/") !== -1) {
            var currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set("section", target);
            window.history.replaceState({}, "", currentUrl.toString());
        }
    };

    sidebarLinks.forEach(function (link) {
        link.addEventListener("click", function () {
            switchDashboardPanel(link.getAttribute("data-dashboard-target"), true);
        });
    });

    var currentUrl = new URL(window.location.href);
    var initialTarget = currentUrl.searchParams.get("section") || window.location.hash.replace(/^#/, "");

    if (initialTarget) {
        switchDashboardPanel(initialTarget, false);
    }

    var showDashboardAlert = function (type, message) {
        if (!message) {
            return;
        }

        var dashboardMain = document.querySelector(".dashboard-main");
        var activePanel = document.querySelector("[data-dashboard-panel].is-active");

        if (!dashboardMain || !activePanel) {
            return;
        }

        Array.prototype.forEach.call(dashboardMain.children, function (child) {
            if (child.classList.contains("alert")) {
                child.remove();
            }
        });

        var alert = document.createElement("div");
        alert.className = "alert dashboard-ajax-alert " + (type === "error" ? "alert-error" : "alert-success");
        alert.textContent = message;
        dashboardMain.insertBefore(alert, activePanel);
    };

    document.addEventListener("submit", function (event) {
        var form = event.target.closest("form[data-dashboard-form]");

        if (!form || !window.fetch || !window.FormData) {
            return;
        }

        event.preventDefault();

        var section = form.getAttribute("data-dashboard-form") || "overview";
        var submitButton = form.querySelector("button[type='submit']");
        var originalButtonText = submitButton ? submitButton.textContent : "";

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = "Saving...";
        }

        fetch(form.action, {
            method: "POST",
            body: new FormData(form),
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            credentials: "same-origin"
        }).then(function (response) {
            return response.json();
        }).then(function (payload) {
            if (payload && payload.section) {
                section = payload.section;
            }

            var panel = document.querySelector("[data-dashboard-panel='" + section + "']");

            if (panel && payload && typeof payload.panelHtml === "string" && payload.panelHtml !== "") {
                panel.innerHTML = payload.panelHtml;
                if (window.initializeDashboardFilters) {
                    window.initializeDashboardFilters(panel);
                }
            }

            if (payload && payload.csrfToken) {
                document.querySelectorAll("input[name='_token']").forEach(function (input) {
                    input.value = payload.csrfToken;
                });
            }

            showDashboardAlert(payload && payload.error ? "error" : "success", payload ? (payload.error || payload.status) : "");
            switchDashboardPanel(section, true);
        }).catch(function () {
            form.submit();
        }).finally(function () {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var normalize = function (value) {
        return String(value || "").trim().toLowerCase();
    };

    var applyFilter = function (bar) {
        var scope = bar.parentElement || document;
        var search = normalize((bar.querySelector("[data-filter-search]") || {}).value);
        var status = normalize((bar.querySelector("[data-filter-status]") || {}).value || "all");
        var flow = normalize((bar.querySelector("[data-filter-flow]") || {}).value || "all");
        var lock = normalize((bar.querySelector("[data-filter-lock]") || {}).value || "all");
        var items = scope.querySelectorAll("[data-filter-item]");
        var shown = 0;

        items.forEach(function (item) {
            var textMatches = !search || normalize(item.getAttribute("data-filter-text")).indexOf(search) !== -1;
            var statusMatches = status === "all" || normalize(item.getAttribute("data-filter-status")) === status;
            var flowMatches = flow === "all" || normalize(item.getAttribute("data-filter-flow")) === flow;
            var lockMatches = lock === "all" || normalize(item.getAttribute("data-filter-lock")) === lock;
            var visible = textMatches && statusMatches && flowMatches && lockMatches;

            item.hidden = !visible;

            if (visible) {
                shown += 1;
            }
        });

        var empty = scope.querySelector("[data-filter-empty]");

        if (empty) {
            empty.hidden = shown !== 0;
        }
    };

    window.initializeDashboardFilters = function (root) {
        var filterBars = (root || document).querySelectorAll("[data-dashboard-filter]");

        if (!filterBars.length) {
            return;
        }

        filterBars.forEach(function (bar) {
            if (bar.getAttribute("data-filter-ready") === "true") {
                applyFilter(bar);
                return;
            }

            bar.setAttribute("data-filter-ready", "true");

        bar.querySelectorAll("input, select").forEach(function (field) {
            field.addEventListener("input", function () {
                applyFilter(bar);
            });
            field.addEventListener("change", function () {
                applyFilter(bar);
            });
        });

        var reset = bar.querySelector("[data-filter-reset]");

        if (reset) {
            reset.addEventListener("click", function () {
                bar.querySelectorAll("input, select").forEach(function (field) {
                    if (field.tagName === "SELECT") {
                        field.value = "all";
                    } else {
                        field.value = "";
                    }
                });
                applyFilter(bar);
            });
        }

        applyFilter(bar);
        });
    };

    window.initializeDashboardFilters(document);
});

document.addEventListener("DOMContentLoaded", function () {
    var orderTypeInputs = document.querySelectorAll("[data-order-type-toggle]");

    if (!orderTypeInputs.length) {
        return;
    }

    var syncOrderTypeSections = function () {
        var active = document.querySelector("[data-order-type-toggle]:checked");
        var activeValue = active ? active.value : "togo";

        document.querySelectorAll("[data-order-type-section]").forEach(function (section) {
            var isActive = section.getAttribute("data-order-type-section") === activeValue;

            section.style.display = isActive ? "grid" : "none";

            section.querySelectorAll("input, select, textarea").forEach(function (field) {
                if (isActive) {
                    field.removeAttribute("disabled");
                } else {
                    field.setAttribute("disabled", "disabled");
                }
            });
        });
    };

    orderTypeInputs.forEach(function (input) {
        input.addEventListener("change", syncOrderTypeSections);
    });

    syncOrderTypeSections();
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[data-receipt-input]").forEach(function (input) {
        var wrapper = input.closest(".receipt-upload-area");

        if (!wrapper) {
            return;
        }

        var previewContainer = wrapper.querySelector("[data-receipt-preview]");
        var previewImage = wrapper.querySelector("[data-receipt-preview-image]");
        var removeButton = wrapper.querySelector("[data-receipt-remove]");

        if (!previewContainer || !previewImage || !removeButton) {
            return;
        }

        input.addEventListener("change", function () {
            var file = input.files && input.files[0] ? input.files[0] : null;

            if (!file) {
                previewContainer.hidden = true;
                previewImage.removeAttribute("src");
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert("Receipt image must be 5MB or smaller.");
                input.value = "";
                previewContainer.hidden = true;
                previewImage.removeAttribute("src");
                return;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                previewImage.src = event.target && typeof event.target.result === "string" ? event.target.result : "";
                previewContainer.hidden = false;
            };
            reader.readAsDataURL(file);
        });

        removeButton.addEventListener("click", function () {
            input.value = "";
            previewContainer.hidden = true;
            previewImage.removeAttribute("src");
        });
    });

    ["checkout-form", "reservation-checkout-form"].forEach(function (formId) {
        var form = document.getElementById(formId);

        if (!form) {
            return;
        }

        form.addEventListener("submit", function (event) {
            var receiptInput = form.querySelector("[data-receipt-input]");

            if (receiptInput && (!receiptInput.files || receiptInput.files.length === 0)) {
                event.preventDefault();
                alert("Please upload your payment receipt before completing the order.");
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var input = document.querySelector("[data-reservation-date-input]");
    var calendar = document.querySelector("[data-reservation-calendar]");

    if (!input || !calendar) {
        return;
    }

    var monthLabel = calendar.querySelector("[data-calendar-month]");
    var daysGrid = calendar.querySelector("[data-calendar-days]");
    var prevButton = calendar.querySelector("[data-calendar-prev]");
    var nextButton = calendar.querySelector("[data-calendar-next]");
    var monthFormatter = new Intl.DateTimeFormat("en-PH", {
        month: "long",
        year: "numeric"
    });

    var parseDate = function (value) {
        var parts = String(value || "").split("-");

        if (parts.length !== 3) {
            return null;
        }

        var year = Number(parts[0]);
        var month = Number(parts[1]) - 1;
        var day = Number(parts[2]);
        var parsed = new Date(year, month, day);

        if (parsed.getFullYear() !== year || parsed.getMonth() !== month || parsed.getDate() !== day) {
            return null;
        }

        return parsed;
    };

    var formatDate = function (date) {
        var month = String(date.getMonth() + 1).padStart(2, "0");
        var day = String(date.getDate()).padStart(2, "0");

        return String(date.getFullYear()) + "-" + month + "-" + day;
    };

    var today = parseDate(calendar.getAttribute("data-min-date")) || new Date();
    today = new Date(today.getFullYear(), today.getMonth(), today.getDate());

    var selectedDate = parseDate(input.value) || today;

    if (selectedDate < today) {
        selectedDate = today;
        input.value = formatDate(today);
    }

    var visibleMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);

    var renderCalendar = function () {
        var firstDay = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth(), 1);
        var startDate = new Date(firstDay);
        var selectedValue = formatDate(selectedDate);
        var todayValue = formatDate(today);

        startDate.setDate(startDate.getDate() - firstDay.getDay());

        monthLabel.textContent = monthFormatter.format(visibleMonth);
        daysGrid.innerHTML = "";

        for (var index = 0; index < 42; index += 1) {
            var date = new Date(startDate);
            var button = document.createElement("button");
            var dateValue = formatDate(date);

            date.setDate(startDate.getDate() + index);
            dateValue = formatDate(date);

            button.type = "button";
            button.className = "reservation-calendar__day";
            button.textContent = String(date.getDate());
            button.setAttribute("data-calendar-date", dateValue);
            button.setAttribute("aria-label", date.toLocaleDateString("en-PH", {
                weekday: "long",
                month: "long",
                day: "numeric",
                year: "numeric"
            }));

            if (date.getMonth() !== visibleMonth.getMonth()) {
                button.classList.add("is-muted");
            }

            if (dateValue === todayValue) {
                button.classList.add("is-today");
            }

            if (dateValue === selectedValue) {
                button.classList.add("is-selected");
                button.setAttribute("aria-pressed", "true");
            } else {
                button.setAttribute("aria-pressed", "false");
            }

            if (date < today) {
                button.disabled = true;
            }

            daysGrid.appendChild(button);
        }

        prevButton.disabled = visibleMonth.getFullYear() === today.getFullYear() && visibleMonth.getMonth() === today.getMonth();
    };

    input.classList.add("is-enhanced");
    input.type = "hidden";
    calendar.hidden = false;
    renderCalendar();

    daysGrid.addEventListener("click", function (event) {
        var button = event.target.closest("[data-calendar-date]");

        if (!button || button.disabled) {
            return;
        }

        var nextDate = parseDate(button.getAttribute("data-calendar-date"));

        if (!nextDate) {
            return;
        }

        selectedDate = nextDate;
        visibleMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
        input.value = formatDate(selectedDate);
        input.dispatchEvent(new Event("change", { bubbles: true }));
        renderCalendar();
    });

    prevButton.addEventListener("click", function () {
        visibleMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() - 1, 1);
        renderCalendar();
    });

    nextButton.addEventListener("click", function () {
        visibleMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() + 1, 1);
        renderCalendar();
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var timeInput = document.querySelector("[data-reservation-time-input]");
    var timePicker = document.querySelector("[data-reservation-time-picker]");

    if (!timeInput || !timePicker) {
        return;
    }

    var dateInput = document.querySelector("[data-reservation-date-input]");
    var slotsGrid = timePicker.querySelector("[data-time-slots]");

    if (!slotsGrid) {
        return;
    }

    var clockReadout = document.createElement("div");
    var clockHand = document.createElement("span");
    var clockDisplay = document.createElement("div");
    var displayHour = document.createElement("span");
    var displayMinute = document.createElement("span");
    var meridiemToggle = document.createElement("div");
    var amButton = document.createElement("button");
    var pmButton = document.createElement("button");

    clockReadout.className = "reservation-time-picker__readout";
    clockReadout.setAttribute("aria-live", "polite");
    clockHand.className = "reservation-time-picker__hand";
    clockHand.setAttribute("aria-hidden", "true");
    clockDisplay.className = "reservation-time-picker__display";
    displayHour.className = "reservation-time-picker__display-hour";
    displayMinute.className = "reservation-time-picker__display-minute";
    meridiemToggle.className = "reservation-time-picker__meridiem";
    meridiemToggle.setAttribute("aria-label", "Choose AM or PM");
    amButton.type = "button";
    amButton.className = "reservation-time-picker__period";
    amButton.setAttribute("data-time-period", "am");
    amButton.textContent = "AM";
    pmButton.type = "button";
    pmButton.className = "reservation-time-picker__period";
    pmButton.setAttribute("data-time-period", "pm");
    pmButton.textContent = "PM";
    clockDisplay.appendChild(displayHour);
    clockDisplay.appendChild(document.createTextNode(":"));
    clockDisplay.appendChild(displayMinute);
    meridiemToggle.appendChild(amButton);
    meridiemToggle.appendChild(pmButton);
    clockDisplay.appendChild(meridiemToggle);
    timePicker.insertBefore(clockDisplay, slotsGrid);
    slotsGrid.appendChild(clockHand);
    slotsGrid.appendChild(clockReadout);
    var activePeriod = "am";

    var padTime = function (value) {
        return String(value).padStart(2, "0");
    };

    var formatTime = function (minutes) {
        var hour = Math.floor(minutes / 60);
        var minute = minutes % 60;

        return padTime(hour) + ":" + padTime(minute);
    };

    var parseTime = function (value) {
        var parts = String(value || "").split(":");

        if (parts.length < 2) {
            return null;
        }

        var hour = Number(parts[0]);
        var minute = Number(parts[1]);

        if (!Number.isInteger(hour) || !Number.isInteger(minute) || hour < 0 || hour > 23 || minute < 0 || minute > 59) {
            return null;
        }

        return (hour * 60) + minute;
    };

    activePeriod = parseTime(timeInput.value) >= 720 ? "pm" : "am";

    var formatTimeLabel = function (value) {
        var minutes = parseTime(value);

        if (minutes === null) {
            return value;
        }

        var hour = Math.floor(minutes / 60);
        var minute = minutes % 60;
        var suffix = hour >= 12 ? "PM" : "AM";
        var displayHour = hour % 12 || 12;

        return String(displayHour) + ":" + padTime(minute) + " " + suffix;
    };

    var formatClockSlotLabel = function (minutes) {
        var hour = Math.floor(minutes / 60);
        var minute = minutes % 60;
        var displayHour = hour % 12 || 12;

        if (minute === 30) {
            return "";
        }

        return String(displayHour);
    };

    var todayValue = function () {
        var today = new Date();

        return String(today.getFullYear()) + "-" + padTime(today.getMonth() + 1) + "-" + padTime(today.getDate());
    };

    var currentMinuteOfDay = function () {
        var now = new Date();

        return (now.getHours() * 60) + now.getMinutes();
    };

    var isSelectedDateToday = function () {
        return dateInput && dateInput.value === todayValue();
    };

    var renderTimeSlots = function () {
        var selectedTime = timeInput.value;
        var selectedMinutes = parseTime(selectedTime);
        var minimumMinutes = isSelectedDateToday() ? currentMinuteOfDay() : -1;
        var firstAvailable = "";
        var periodStart = activePeriod === "pm" ? 720 : 0;
        var selectedIsVisible = selectedMinutes !== null && selectedMinutes >= periodStart && selectedMinutes < periodStart + 720;

        slotsGrid.innerHTML = "";
        slotsGrid.appendChild(clockHand);
        slotsGrid.appendChild(clockReadout);
        amButton.classList.toggle("is-selected", activePeriod === "am");
        pmButton.classList.toggle("is-selected", activePeriod === "pm");
        amButton.setAttribute("aria-pressed", activePeriod === "am" ? "true" : "false");
        pmButton.setAttribute("aria-pressed", activePeriod === "pm" ? "true" : "false");

        for (var periodMinutes = 0; periodMinutes < 720; periodMinutes += 30) {
            var minutes = periodStart + periodMinutes;
            var timeValue = formatTime(minutes);
            var button = document.createElement("button");
            var isDisabled = minutes <= minimumMinutes;
            var angle = ((periodMinutes / 720) * Math.PI * 2) - (Math.PI / 2);
            var radius = minutes % 60 === 0 ? 40 : 33;
            var x = 50 + (Math.cos(angle) * radius);
            var y = 50 + (Math.sin(angle) * radius);

            button.type = "button";
            button.className = "reservation-time-picker__slot";
            button.textContent = formatClockSlotLabel(minutes);
            button.style.left = String(x) + "%";
            button.style.top = String(y) + "%";
            button.setAttribute("data-time-value", timeValue);
            button.setAttribute("aria-label", formatTimeLabel(timeValue));
            button.setAttribute("aria-pressed", timeValue === selectedTime ? "true" : "false");

            if (isDisabled) {
                button.disabled = true;
            } else if (!firstAvailable) {
                firstAvailable = timeValue;
            }

            if (selectedIsVisible && minutes === selectedMinutes) {
                button.classList.add("is-selected");
            }

            slotsGrid.appendChild(button);
        }

        if (selectedMinutes !== null) {
            periodStart = activePeriod === "pm" ? 720 : 0;
            var selectedPeriodMinutes = selectedIsVisible ? selectedMinutes - periodStart : 0;
            clockHand.style.transform = "translateX(-50%) rotate(" + String((selectedPeriodMinutes / 720) * 360) + "deg)";
            clockReadout.textContent = formatTimeLabel(selectedTime);
            displayHour.textContent = String(Math.floor(selectedMinutes / 60) % 12 || 12);
            displayMinute.textContent = padTime(selectedMinutes % 60);
        } else {
            clockHand.style.transform = "translateX(-50%) rotate(0deg)";
            clockReadout.textContent = "Choose a time";
            displayHour.textContent = "--";
            displayMinute.textContent = "--";
        }

        if ((!selectedTime || selectedMinutes === null || selectedMinutes <= minimumMinutes) && firstAvailable) {
            timeInput.value = firstAvailable;
            timeInput.dispatchEvent(new Event("change", { bubbles: true }));
            renderTimeSlots();
        }
    };

    timeInput.classList.add("is-enhanced");
    timeInput.type = "hidden";
    timePicker.hidden = false;
    renderTimeSlots();

    slotsGrid.addEventListener("click", function (event) {
        var button = event.target.closest("[data-time-value]");

        if (!button || button.disabled) {
            return;
        }

        timeInput.value = button.getAttribute("data-time-value") || "";
        timeInput.dispatchEvent(new Event("change", { bubbles: true }));
        renderTimeSlots();
    });

    meridiemToggle.addEventListener("click", function (event) {
        var button = event.target.closest("[data-time-period]");

        if (!button) {
            return;
        }

        activePeriod = button.getAttribute("data-time-period") === "pm" ? "pm" : "am";
        var selectedMinutes = parseTime(timeInput.value);

        if (selectedMinutes !== null) {
            var periodMinutes = selectedMinutes % 720;
            var nextMinutes = (activePeriod === "pm" ? 720 : 0) + periodMinutes;

            if (!isSelectedDateToday() || nextMinutes > currentMinuteOfDay()) {
                timeInput.value = formatTime(nextMinutes);
                timeInput.dispatchEvent(new Event("change", { bubbles: true }));
            }
        }

        renderTimeSlots();
    });

    if (dateInput) {
        dateInput.addEventListener("change", renderTimeSlots);
    }
});

document.addEventListener("DOMContentLoaded", function () {
    var orderModal = document.getElementById("dashboard-order-modal");
    var reservationModal = document.getElementById("dashboard-reservation-modal");
    var activeModal = null;

    var formatCurrency = function (value) {
        var amount = Number(value || 0);
        return "PHP " + amount.toLocaleString("en-PH", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    var formatDateTime = function (value, options) {
        if (!value) {
            return "-";
        }

        var normalized = String(value).replace(" ", "T");
        var date = new Date(normalized);

        if (Number.isNaN(date.getTime())) {
            return String(value);
        }

        return date.toLocaleString("en-PH", options || {
            month: "short",
            day: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit"
        });
    };

    var applyStatusBadge = function (element, status) {
        if (!element) {
            return;
        }

        var normalized = String(status || "pending").toLowerCase();
        element.className = "status-pill status-pill--" + normalized;
        element.textContent = normalized.charAt(0).toUpperCase() + normalized.slice(1);
    };

    var statusMarkup = function (status) {
        var normalized = String(status || "pending").toLowerCase();
        return '<span class="status-pill status-pill--' + normalized + '">' +
            normalized.charAt(0).toUpperCase() + normalized.slice(1) +
        '</span>';
    };

    var openModal = function (modal) {
        if (!modal) {
            return;
        }

        modal.classList.add("is-open");
        modal.setAttribute("aria-hidden", "false");
        document.body.classList.add("modal-open");
        activeModal = modal;
    };

    var closeModal = function (modal) {
        if (!modal) {
            return;
        }

        modal.classList.remove("is-open");
        modal.setAttribute("aria-hidden", "true");
        if (activeModal === modal) {
            activeModal = null;
        }
        if (!document.querySelector(".dashboard-modal.is-open")) {
            document.body.classList.remove("modal-open");
        }
    };

    var renderOrderItems = function (container, items) {
        if (!container) {
            return;
        }

        if (!Array.isArray(items) || !items.length) {
            container.innerHTML = '<p class="dashboard-modal__empty">No item lines recorded for this order.</p>';
            return;
        }

        container.innerHTML = items.map(function (item) {
            return '<article class="dashboard-modal__item">' +
                '<div>' +
                    '<h4>' + String(item.menu_item_name || "Menu item") + '</h4>' +
                    '<p>' + String(item.size || "Default") + ' · Qty ' + String(item.quantity || 0) + '</p>' +
                '</div>' +
                '<strong>' + formatCurrency(item.subtotal || 0) + '</strong>' +
            '</article>';
        }).join("");
    };

    var renderReservationOrders = function (container, orders) {
        if (!container) {
            return;
        }

        if (!Array.isArray(orders) || !orders.length) {
            container.innerHTML = '<p class="dashboard-modal__empty">No linked orders were found for this reservation yet.</p>';
            return;
        }

        container.innerHTML = orders.map(function (order) {
            var itemMarkup = Array.isArray(order.items) && order.items.length
                ? '<ul class="order-line-list order-line-list--compact">' + order.items.map(function (item) {
                    return '<li><span>' + String(item.menu_item_name || "Menu item") + ' x' + String(item.quantity || 0) + '</span><span>' + String(item.size || "Default") + '</span></li>';
                }).join("") + '</ul>'
                : '';

            return '<article class="dashboard-modal__item dashboard-modal__item--stacked">' +
                '<div class="dashboard-modal__item-head">' +
                    '<div>' +
                        '<h4>Order ' + String(order.order_number || "-") + '</h4>' +
                        '<p>' + formatDateTime(order.created_at) + '</p>' +
                    '</div>' +
                    '<div class="dashboard-modal__item-badges">' +
                        statusMarkup(order.status || "pending") +
                        statusMarkup(order.payment_status || "pending") +
                    '</div>' +
                '</div>' +
                '<p class="dashboard-modal__item-total">' + formatCurrency(order.total_amount || 0) + '</p>' +
                itemMarkup +
            '</article>';
        }).join("");
    };

    document.addEventListener("click", function (event) {
        var button = event.target.closest("[data-open-order-modal]");

        if (button) {
            if (!orderModal) {
                return;
            }

            var payload = button.getAttribute("data-order-details");
            if (!payload) {
                return;
            }

            var order = JSON.parse(payload);
            document.getElementById("dashboard-order-number").textContent = String(order.order_number || "-");
            document.getElementById("dashboard-order-date").textContent = formatDateTime(order.created_at);
            document.getElementById("dashboard-order-flow").textContent = order.reservation_id ? "Reservation-linked order" : "Direct menu checkout";
            document.getElementById("dashboard-order-total").textContent = formatCurrency(order.total_amount || 0);
            document.getElementById("dashboard-order-item-count").textContent = String((order.items || []).length) + " item(s)";
            applyStatusBadge(document.getElementById("dashboard-order-status-badge"), order.status || "pending");
            applyStatusBadge(document.getElementById("dashboard-order-payment-badge"), order.payment_status || "pending");
            renderOrderItems(document.getElementById("dashboard-order-items"), order.items || []);

            var receiptLink = document.getElementById("dashboard-order-receipt-link");
            if (receiptLink) {
                if (order.receipt_image) {
                    receiptLink.href = window.location.origin + window.location.pathname.split("/dashboard")[0] + "/public/uploads/receipts/" + encodeURIComponent(order.receipt_image);
                    receiptLink.hidden = false;
                } else {
                    receiptLink.hidden = true;
                    receiptLink.removeAttribute("href");
                }
            }

            openModal(orderModal);
            return;
        }

        button = event.target.closest("[data-open-reservation-modal]");

        if (button) {
            if (!reservationModal) {
                return;
            }

            var payload = button.getAttribute("data-reservation-details");
            if (!payload) {
                return;
            }

            var reservation = JSON.parse(payload);
            document.getElementById("dashboard-reservation-number").textContent = "#" + String(reservation.id || "-");
            document.getElementById("dashboard-reservation-name").textContent =
                [reservation.first_name || "", reservation.last_name || ""].join(" ").trim() || "Guest";
            document.getElementById("dashboard-reservation-schedule").textContent =
                formatDateTime((reservation.date || "") + " " + (reservation.time || ""));
            document.getElementById("dashboard-reservation-guests").textContent = String(reservation.guests || 0) + " guest(s)";
            document.getElementById("dashboard-reservation-email").textContent = String(reservation.email || "-");
            document.getElementById("dashboard-reservation-phone").textContent = String(reservation.phone || "-");
            document.getElementById("dashboard-reservation-created-at").textContent = formatDateTime(reservation.created_at);
            document.getElementById("dashboard-reservation-order-count").textContent = String((reservation.orders || []).length) + " order(s)";
            applyStatusBadge(document.getElementById("dashboard-reservation-status-badge"), reservation.status || "pending");
            renderReservationOrders(document.getElementById("dashboard-reservation-orders"), reservation.orders || []);

            openModal(reservationModal);
        }
    });

    document.querySelectorAll("[data-dashboard-modal-close]").forEach(function (button) {
        button.addEventListener("click", function () {
            closeModal(button.closest(".dashboard-modal"));
        });
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && activeModal) {
            closeModal(activeModal);
        }
    });
});
