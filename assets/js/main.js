/**
 * Quran Simple - Main JavaScript
 *
 * @package QuranSimple
 * @since 2.2.0
 */

(function($) {
    'use strict';

    // Get variables from WordPress
    var ajaxurl = quranSimple.ajaxurl;
    var currentSura = quranSimple.currentSura;

    // ============ Document Ready ============
    $(document).ready(function() {
        // Initialize dark mode
        initDarkMode();

        // Initialize font size
        initFontSize();

        // Initialize language visibility from localStorage
        initLanguageToggles();

        // Check for Arabic-only mode on load
        checkArabicOnlyMode();

        // Initialize bookmarks
        initBookmarks();

        // Show last read banner / auto-scroll to last position
        // Delay scroll tracking until resume is done to prevent overwriting saved position
        resumeLastRead();
        setTimeout(function() {
            isResuming = false;
            trackScrollPosition();
        }, 2000);

        // Initialize challenge
        initChallenge();
    });

    // ============ Dark Mode Functions ============
    function initDarkMode() {
        var isDark = localStorage.getItem("quran_dark_mode") === "true";
        if (isDark) {
            $(".quran-container").addClass("dark-mode");
            updateDarkModeIcon(true);
        }
    }

    window.toggleDarkMode = function() {
        var container = $(".quran-container");
        var isDark = container.hasClass("dark-mode");
        if (isDark) {
            container.removeClass("dark-mode");
            localStorage.setItem("quran_dark_mode", "false");
            updateDarkModeIcon(false);
        } else {
            container.addClass("dark-mode");
            localStorage.setItem("quran_dark_mode", "true");
            updateDarkModeIcon(true);
        }
    };

    function updateDarkModeIcon(isDark) {
        var icon = isDark ?
            '<svg viewBox="0 0 24 24"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37c-.39-.39-1.03-.39-1.41 0-.39.39-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0 .39-.39.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96c.39-.39.39-1.03 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36c.39-.39.39-1.03 0-1.41-.39-.39-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z"/></svg>' :
            '<svg viewBox="0 0 24 24"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg>';
        $("#dark-mode-icon").html(icon);
        $("#dark-mode-text").text(isDark ? "Light Mode" : "Dark Mode");
    }

    // ============ Font Size Functions ============
    var fontSize = 1.5;

    function initFontSize() {
        var saved = localStorage.getItem("quran_font_size");
        if (saved) {
            fontSize = parseFloat(saved);
            applyFontSizes();
        }
        updateFontDisplay();
    }

    window.changeFontSize = function(delta) {
        fontSize = Math.max(0.8, Math.min(4, fontSize + delta * 0.2));
        applyFontSizes();
        localStorage.setItem("quran_font_size", fontSize);
        updateFontDisplay();
    };

    function applyFontSizes() {
        $(".quran, .englishtrans, .trans").css("font-size", fontSize + "rem");
    }

    function updateFontDisplay() {
        $("#font-size-display").text(Math.round(fontSize / 1.5 * 100) + "%");
    }

    // ============ Last Read Functions ============
    function saveLastRead(sura, aya) {
        var lastRead = { sura: sura, aya: aya, timestamp: Date.now() };
        localStorage.setItem("quran_last_read", JSON.stringify(lastRead));
    }

    function getLastRead() {
        try {
            return JSON.parse(localStorage.getItem("quran_last_read"));
        } catch(e) {
            return null;
        }
    }

    var isResuming = true; // Start as true to prevent early tracking

    function resumeLastRead() {
        var lastRead = getLastRead();
        if (!lastRead) return;

        var lastSura = String(lastRead.sura);
        var currSura = String(currentSura);

        if (lastSura !== currSura) {
            // Different surah — show banner to go back
            $("#last-read-sura").text("Surah " + lastRead.sura + ", Ayah " + lastRead.aya);
            $("#last-read-banner").addClass("active");
        } else if (lastRead.aya && Number(lastRead.aya) > 1) {
            // Same surah — auto-scroll to last read ayah
            var target = $("#aya-" + currSura + "-" + lastRead.aya);
            if (target.length) {
                isResuming = true;
                setTimeout(function() {
                    var scrollTo = target.offset().top - 100;
                    window.scrollTo({ top: scrollTo, behavior: "smooth" });
                    setTimeout(function() { isResuming = false; }, 1000);
                }, 500);
            }
        }
    }

    window.continueReading = function() {
        var lastRead = getLastRead();
        if (lastRead) {
            goToAya(lastRead.sura, lastRead.aya);
        }
    };

    window.dismissLastRead = function() {
        $("#last-read-banner").removeClass("active");
    };

    function trackScrollPosition() {
        var scrollTimer;
        $(window).on("scroll", function() {
            if (isResuming) return;
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function() {
                if (isResuming) return;
                var viewportTop = $(window).scrollTop() + 150;
                $(".aya").each(function() {
                    var ayaTop = $(this).offset().top;
                    if (ayaTop <= viewportTop && ayaTop + $(this).height() > viewportTop) {
                        var sura = $(this).find(".verse-data").data("sura");
                        var aya = $(this).find(".verse-data").data("aya");
                        if (sura && aya) {
                            saveLastRead(sura, aya);
                        }
                        return false;
                    }
                });
            }, 200);
        });
    }

    // ============ Arabic-Only Mode (Mushaf Style) ============
//     function checkArabicOnlyMode() {
//         var arabicChecked = $("input.arabic").is(":checked");
//         var englishChecked = $("input.english").is(":checked");
//         var urduChecked = $("input.urdu").is(":checked");

//         if (arabicChecked && !englishChecked && !urduChecked) {
//             $(".quran-container").addClass("arabic-only-mode");
//         } else {
//             $(".quran-container").removeClass("arabic-only-mode");
//         }
//     }
	function checkArabicOnlyMode() {
		let arabicChecked = $("input.arabic").is(":checked");
		let englishChecked = $("input.english").is(":checked");
		let urduChecked = $("input.urdu").is(":checked");

		if (arabicChecked && !englishChecked && !urduChecked) {
			$(".quran-container").addClass("arabic-only-mode");

			// 🔥 Swap: ayaNum after ayaText
			$(".quran").each(function () {
				let ayaNum = $(this).find(".ayaNum");
				let ayaText = $(this).find(".ayaText");

				// Move ayaNum after ayaText unconditionally
				ayaText.after(ayaNum);
			});

		} else {
			$(".quran-container").removeClass("arabic-only-mode");

			// 🔄 Revert: ayaNum always before ayaText
			$(".quran").each(function () {
				let ayaNum = $(this).find(".ayaNum");
				let ayaText = $(this).find(".ayaText");

				// Move ayaNum before ayaText unconditionally
				ayaNum.insertBefore(ayaText);
			});
		}
	}


    // ============ Language Toggle Functions ============
    function initLanguageToggles() {
        // Get saved preferences from localStorage (default: Arabic on, others off)
        var showArabic = localStorage.getItem("quran_show_arabic") !== "false";
        var showEnglish = localStorage.getItem("quran_show_english") === "true";
        var showUrdu = localStorage.getItem("quran_show_urdu") === "true";

        // Ensure at least one is selected (default to Arabic if none)
        if (!showArabic && !showEnglish && !showUrdu) {
            showArabic = true;
            localStorage.setItem("quran_show_arabic", "true");
        }

        // Set checkbox states
        $("input.arabic").prop("checked", showArabic);
        $("input.english").prop("checked", showEnglish);
        $("input.urdu").prop("checked", showUrdu);

        // Remove arabic-only-mode class first (it uses !important)
        $(".quran-container").removeClass("arabic-only-mode");

        // Apply visibility using inline styles to override any CSS
        if (showArabic) {
            $(".aya .quran").css("display", "");
        } else {
            $(".aya .quran").css("display", "none");
        }

        if (showEnglish) {
            $(".aya .englishtrans").css("display", "");
        } else {
            $(".aya .englishtrans").css("display", "none");
        }

        if (showUrdu) {
            $(".aya .trans").css("display", "");
        } else {
            $(".aya .trans").css("display", "none");
        }

        // Now check if we should enable arabic-only mode
        checkArabicOnlyMode();
    }

    // Helper function to count OTHER checked checkboxes (excluding current)
    function getOtherCheckedCount(excludeClass) {
        var count = 0;
        if (excludeClass !== "arabic" && $("input.arabic").is(":checked")) count++;
        if (excludeClass !== "english" && $("input.english").is(":checked")) count++;
        if (excludeClass !== "urdu" && $("input.urdu").is(":checked")) count++;
        return count;
    }

    $(document).on("change", ".arabic", function() {
        var isChecked = $(this).is(":checked");

        // If trying to uncheck and no others are checked, prevent it
        if (!isChecked && getOtherCheckedCount("arabic") === 0) {
            $(this).prop("checked", true);
            showToast("At least one language must be selected");
            return;
        }

        // Save to localStorage
        localStorage.setItem("quran_show_arabic", isChecked ? "true" : "false");

        // Remove arabic-only-mode first to allow changes
        $(".quran-container").removeClass("arabic-only-mode");

        // Update visibility
        $(".aya .quran").css("display", isChecked ? "" : "none");

        checkArabicOnlyMode();
    });

    $(document).on("change", ".urdu", function() {
        var isChecked = $(this).is(":checked");

        // If trying to uncheck and no others are checked, prevent it
        if (!isChecked && getOtherCheckedCount("urdu") === 0) {
            $(this).prop("checked", true);
            showToast("At least one language must be selected");
            return;
        }

        // Save to localStorage
        localStorage.setItem("quran_show_urdu", isChecked ? "true" : "false");

        // Remove arabic-only-mode first to allow changes
        $(".quran-container").removeClass("arabic-only-mode");

        // Update visibility
        $(".aya .trans").css("display", isChecked ? "" : "none");

        checkArabicOnlyMode();
    });

    $(document).on("change", ".english", function() {
        var isChecked = $(this).is(":checked");

        // If trying to uncheck and no others are checked, prevent it
        if (!isChecked && getOtherCheckedCount("english") === 0) {
            $(this).prop("checked", true);
            showToast("At least one language must be selected");
            return;
        }

        // Save to localStorage
        localStorage.setItem("quran_show_english", isChecked ? "true" : "false");

        // Remove arabic-only-mode first to allow changes
        $(".quran-container").removeClass("arabic-only-mode");

        // Update visibility
        $(".aya .englishtrans").css("display", isChecked ? "" : "none");

        checkArabicOnlyMode();
    });

    // ============ Navigation Functions ============
    function navigateToSura(sura, aya) {
        var url = new URL(window.location.href);
        url.searchParams.set("sura", sura);
        if (aya && aya > 1) {
            url.hash = "aya-" + sura + "-" + aya;
        } else {
            url.hash = "";
        }
        window.location.href = url.toString();
    }

    $(document).on("change", "#sura-select", function() {
        navigateToSura($(this).val());
    });

    $(document).on("change", "#juz-select", function() {
        var selected = $(this).find(":selected");
        var sura = selected.data("sura");
        var aya = selected.data("aya");
        navigateToSura(sura, aya);
    });

    // ============ Search Functions ============
    function searchQuran() {
        var query = $("#quran-search").val().trim();
        if (query.length < 2) {
            return;
        }

        $("body").append("<div class='loadingdiv'>Searching...</div>");

        $.post(ajaxurl, { action: "search_quran", query: query }, function(response) {
            $(".loadingdiv").remove();
            try {
                var data = JSON.parse(response);
                displaySearchResults(data);
            } catch(e) {
                console.error("Search error:", e);
            }
        });
    }

    // Arabic diacritics pattern (tashkeel) - matches zero or more diacritics between characters
    var arabicDiacritics = '[\u064B-\u065F\u0610-\u061A\u0670\u06D6-\u06ED]*';

    // Normalize Arabic/Urdu character variants for matching
    function normalizeArabicChar(ch) {
        var map = {
            '\u06CC': '\u064A', // ی -> ي
            '\u06D2': '\u064A', // ے -> ي
            '\u06A9': '\u0643', // ک -> ك
            '\u06C1': '\u0647', // ہ -> ه
            '\u06C3': '\u0647', // ۃ -> ه
            '\u0629': '\u0647', // ة -> ه
            '\u0623': '\u0627', // أ -> ا
            '\u0625': '\u0627', // إ -> ا
            '\u0622': '\u0627', // آ -> ا
            '\u0649': '\u064A'  // ى -> ي
        };
        return map[ch] || ch;
    }

    function highlightText(text, query) {
        if (!query) return text;

        // Check if query contains Arabic/Urdu characters
        var isArabic = /[\u0600-\u06FF]/.test(query);

        if (isArabic) {
            // Build regex that allows optional diacritics between each character
            // and normalizes character variants
            var pattern = '';
            for (var i = 0; i < query.length; i++) {
                var ch = query[i];
                if (ch === ' ') {
                    pattern += '\\s+' + arabicDiacritics;
                } else {
                    var normalized = normalizeArabicChar(ch);
                    // Match either the original char or its normalized form, followed by optional diacritics
                    if (normalized !== ch) {
                        pattern += '[' + ch.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + normalized.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ']' + arabicDiacritics;
                    } else {
                        pattern += ch.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + arabicDiacritics;
                    }
                }
            }
            var regex = new RegExp('(' + pattern + ')', 'gi');
            return text.replace(regex, '<mark class="search-highlight">$1</mark>');
        } else {
            // Latin/English - simple exact match
            var escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var regex = new RegExp('(' + escaped + ')', 'gi');
            return text.replace(regex, '<mark class="search-highlight">$1</mark>');
        }
    }

    function displaySearchResults(data) {
        var query = $("#quran-search").val().trim();
        var html = "";
        if (data.results && data.results.length > 0) {
            html += "<div class='search-count'>Found " + data.count + " results</div>";
            data.results.forEach(function(r) {
                var textClass = r.type === "arabic" ? "arabic" : (r.type === "urdu" ? "urdu" : "");
                var displayText = r.text.substring(0, 150) + (r.text.length > 150 ? "..." : "");
                displayText = highlightText(displayText, query);
                html += "<div class='search-result-item' onclick='goToAya(" + r.sura + ", " + r.aya + ", \"" + encodeURIComponent(query) + "\")'>";
                html += "<div class='search-result-ref'>" + r.suraName + " " + r.sura + ":" + r.aya + " (" + r.type + ")</div>";
                html += "<div class='search-result-text " + textClass + "'>" + displayText + "</div>";
                html += "</div>";
            });
        } else {
            html = "<div class='no-results'>No results found</div>";
        }
        $("#search-results").html(html).addClass("active");
    }

    window.goToAya = function(sura, aya, highlight) {
        var url = new URL(window.location.href);
        url.searchParams.set("sura", sura);
        if (highlight) {
            url.searchParams.set("highlight", decodeURIComponent(highlight));
        }
        url.hash = "aya-" + sura + "-" + aya;
        window.location.href = url.toString();
    };

    $(document).on("click", "#search-btn", function() {
        searchQuran();
    });

    $(document).on("keypress", "#quran-search", function(e) {
        if (e.which === 13) {
            searchQuran();
        }
    });

    // Bug 2: Live search with debounce
    var searchDebounceTimer;
    $(document).on("input", "#quran-search", function() {
        var query = $(this).val().trim();
        clearTimeout(searchDebounceTimer);
        if (query.length < 2) {
            $("#search-results").removeClass("active").html("");
            return;
        }
        $("#search-results").html("<div class='search-loading'>Searching...</div>").addClass("active");
        searchDebounceTimer = setTimeout(function() {
            searchQuran();
        }, 300);
    });

    $(document).on("click", function(e) {
        if (!$(e.target).closest(".search-container, #search-results").length) {
            $("#search-results").removeClass("active");
        }
    });

    // ============ Bookmark Functions ============
    function getBookmarks() {
        try {
            return JSON.parse(localStorage.getItem("quran_bookmarks") || "[]");
        } catch(e) {
            return [];
        }
    }

    function saveBookmarks(bookmarks) {
        localStorage.setItem("quran_bookmarks", JSON.stringify(bookmarks));
    }

    window.toggleBookmark = function(sura, aya) {
        var bookmarks = getBookmarks();
        var key = sura + ":" + aya;
        var index = bookmarks.indexOf(key);

        if (index > -1) {
            bookmarks.splice(index, 1);
            showToast("Bookmark removed");
        } else {
            bookmarks.push(key);
            showToast("Bookmark added");
        }

        saveBookmarks(bookmarks);
        updateBookmarkIcon(sura, aya);
    };

    function updateBookmarkIcon(sura, aya) {
        var bookmarks = getBookmarks();
        var key = sura + ":" + aya;
        var btn = $(".bookmark-btn[data-sura='" + sura + "'][data-aya='" + aya + "']");

        if (bookmarks.indexOf(key) > -1) {
            btn.addClass("active");
        } else {
            btn.removeClass("active");
        }
    }

    function initBookmarks() {
        var bookmarks = getBookmarks();
        bookmarks.forEach(function(key) {
            var parts = key.split(":");
            if (parts.length === 2) {
                var sura = parseInt(parts[0]);
                var aya = parseInt(parts[1]);
                $(".bookmark-btn[data-sura='" + sura + "'][data-aya='" + aya + "']").addClass("active");
            }
        });
    }

    window.showBookmarksPanel = function() {
        var bookmarks = getBookmarks();
        var html = "";

        if (bookmarks.length > 0) {
            html = "<div class='bookmarks-list'>";
            bookmarks.forEach(function(key) {
                var parts = key.split(":");
                html += "<div class='bookmark-item'>";
                html += "<a href='javascript:void(0)' class='bookmark-link' onclick='goToAya(" + parts[0] + ", " + parts[1] + ")'>Surah " + parts[0] + ", Ayah " + parts[1] + "</a>";
                html += "<button class='remove-bookmark' onclick='removeBookmark(\"" + key + "\")'>Remove</button>";
                html += "</div>";
            });
            html += "</div>";
        } else {
            html = "<div class='no-bookmarks'>No bookmarks yet. Click the bookmark icon on any verse to save it.</div>";
        }

        $("#bookmarks-list-container").html(html);
        $("#bookmarks-panel").addClass("active");
    };

    window.removeBookmark = function(key) {
        var bookmarks = getBookmarks();
        var index = bookmarks.indexOf(key);
        if (index > -1) {
            bookmarks.splice(index, 1);
            saveBookmarks(bookmarks);

            var parts = key.split(":");
            updateBookmarkIcon(parseInt(parts[0]), parseInt(parts[1]));
            window.showBookmarksPanel();
            showToast("Bookmark removed");
        }
    };

    $(document).on("click", ".close-bookmarks", function() {
        $("#bookmarks-panel").removeClass("active");
    });

    // ============ Copy & Share Functions ============
    window.copyVerse = function(sura, aya) {
        var verseData = $("#aya-" + sura + "-" + aya + " .verse-data");
        var arabic = verseData.data("arabic");
        var english = verseData.data("english");
        var urdu = verseData.data("urdu");
        var suraName = verseData.data("sura-name");

        var text = arabic + "\n\n" + english + "\n\n" + urdu + "\n\n- " + suraName + " " + sura + ":" + aya;

        navigator.clipboard.writeText(text).then(function() {
            showToast("Verse copied to clipboard!");
        }).catch(function() {
            // Fallback
            var textarea = document.createElement("textarea");
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);
            showToast("Verse copied to clipboard!");
        });
    };

    var shareData = {};

    window.shareVerse = function(sura, aya) {
        var verseData = $("#aya-" + sura + "-" + aya + " .verse-data");
        shareData = {
            sura: sura,
            aya: aya,
            arabic: verseData.data("arabic"),
            english: verseData.data("english"),
            urdu: verseData.data("urdu"),
            suraName: verseData.data("sura-name")
        };
        $("#share-modal").addClass("active");
    };

    window.shareToWhatsApp = function() {
        var text = shareData.arabic + "\n\n" + shareData.english + "\n\n" + shareData.urdu + "\n\n- " + shareData.suraName + " " + shareData.sura + ":" + shareData.aya;
        window.open("https://wa.me/?text=" + encodeURIComponent(text), "_blank");
        closeShareModal();
    };

    window.shareToTwitter = function() {
        var text = shareData.english.substring(0, 200) + "... - " + shareData.suraName + " " + shareData.sura + ":" + shareData.aya;
        window.open("https://twitter.com/intent/tweet?text=" + encodeURIComponent(text), "_blank");
        closeShareModal();
    };

    window.shareToFacebook = function() {
        var text = shareData.english + " - " + shareData.suraName + " " + shareData.sura + ":" + shareData.aya;
        window.open("https://www.facebook.com/sharer/sharer.php?quote=" + encodeURIComponent(text), "_blank");
        closeShareModal();
    };

    window.copyFromModal = function() {
        var text = shareData.arabic + "\n\n" + shareData.english + "\n\n" + shareData.urdu + "\n\n- " + shareData.suraName + " " + shareData.sura + ":" + shareData.aya;
        navigator.clipboard.writeText(text);
        showToast("Verse copied!");
        closeShareModal();
    };

    window.closeShareModal = function() {
        $("#share-modal").removeClass("active");
    };

    $(document).on("click", "#share-modal", function(e) {
        if ($(e.target).is("#share-modal")) {
            closeShareModal();
        }
    });

    // ============ Toast Notification ============
    function showToast(message) {
        $("#toast").text(message).addClass("show");
        setTimeout(function() {
            $("#toast").removeClass("show");
        }, 2500);
    }

    // ============ Scroll to Aya ============
    if (window.location.hash) {
        setTimeout(function() {
            var el = $(window.location.hash);
            if (el.length) {
                $("html, body").animate({ scrollTop: el.offset().top - 100 }, 500);
                el.css("background-color", "#ffffcc");
                setTimeout(function() { el.css("background-color", ""); }, 2000);
            }
        }, 500);
    }

    // ============ Highlight from URL param ============
    (function() {
        var urlParams = new URLSearchParams(window.location.search);
        var highlightWord = urlParams.get("highlight");
        if (!highlightWord) return;

        // Skip if already highlighted by a previous script load
        if (document.querySelectorAll('.search-highlight').length > 0) return;

        function buildHighlightRegex(word) {
            var isArabicQuery = /[\u0600-\u06FF]/.test(word);
            if (isArabicQuery) {
                var pat = '';
                for (var i = 0; i < word.length; i++) {
                    var ch = word[i];
                    if (ch === ' ') {
                        pat += '\\s+' + arabicDiacritics;
                    } else {
                        var norm = normalizeArabicChar(ch);
                        var esc = ch.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                        if (norm !== ch) {
                            var escNorm = norm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                            pat += '[' + esc + escNorm + ']' + arabicDiacritics;
                        } else {
                            pat += esc + arabicDiacritics;
                        }
                    }
                }
                return new RegExp('(' + pat + ')', 'gi');
            } else {
                var escaped = word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                return new RegExp('(' + escaped + ')', 'gi');
            }
        }

        function doHighlight() {
            var regex = buildHighlightRegex(highlightWord);
            var count = 0;
            $(".quran .ayaText, .englishtrans, .trans").each(function() {
                var el = $(this);
                var html = el.html();
                regex.lastIndex = 0;
                if (regex.test(html)) {
                    regex.lastIndex = 0;
                    el.html(html.replace(regex, '<mark class="search-highlight">$1</mark>'));
                    count++;
                }
            });
            return count;
        }

        // Retry mechanism to handle race conditions with duplicate script loading
        var attempts = 0;
        var retryTimer = setInterval(function() {
            attempts++;
            if (document.querySelectorAll('.search-highlight').length > 0 || attempts >= 10) {
                clearInterval(retryTimer);
                return;
            }
            if (document.querySelectorAll('.englishtrans, .trans, .quran').length > 0) {
                doHighlight();
            }
        }, 500);

        // Also try after short delay
        setTimeout(doHighlight, 800);
    })();

        // ============ IMAGE GENERATOR ============
    var imageData = {};
    var currentTemplate = 1;

    var templates = {
        1: { bg1: "#667eea", bg2: "#764ba2", text: "#ffffff" },
        2: { bg1: "#11998e", bg2: "#38ef7d", text: "#ffffff" },
        3: { bg1: "#ee0979", bg2: "#ff6a00", text: "#ffffff" },
        4: { bg1: "#2c3e50", bg2: "#4ca1af", text: "#ffffff" },
        5: { bg1: "#8E2DE2", bg2: "#4A00E0", text: "#ffffff" },
        6: { bg1: "#1a1a2e", bg2: "#16213e", text: "#eaeaea" }
    };

    window.openImageGenerator = function(sura, aya) {
        var verseData = $("#aya-" + sura + "-" + aya + " .verse-data");
        imageData = {
            sura: sura,
            aya: aya,
            arabic: verseData.data("arabic"),
            english: verseData.data("english"),
			urdu: verseData.data("urdu"),
            suraName: verseData.data("sura-name")
        };
        $("#image-modal").addClass("active");
        currentTemplate = 1;
        $(".template-btn").removeClass("active");
        $(".template-1").addClass("active");
        generateVerseImage();
    };

    window.selectTemplate = function(num) {
        currentTemplate = num;
        $(".template-btn").removeClass("active");
        $(".template-" + num).addClass("active");
        generateVerseImage();
    };

    function generateVerseImage() {
        var canvas = document.getElementById("verse-canvas");
        var ctx = canvas.getContext("2d");
        var t = templates[currentTemplate];
        var W = 800;
        var maxW = W - 100;
        var yPadding = 60;
        var sectionGap = 30;
        var footerH = 80;

        var arabicText = imageData.arabic || "";
        var englishText = imageData.english || "";
        var urduText = imageData.urdu || "";

        // Use an offscreen canvas for measuring to avoid reset issues
        var offscreen = document.createElement("canvas");
        offscreen.width = W;
        offscreen.height = 1;
        var mCtx = offscreen.getContext("2d");

        mCtx.font = "36px Muhammadi, Arial";
        var arabicLines = getWrappedLines(mCtx, arabicText, maxW);
        mCtx.font = "italic 22px Calibri, Arial";
        var englishLines = getWrappedLines(mCtx, englishText, maxW);
        mCtx.font = "22px Jameel Noori Nastaleeq, Arial";
        var urduLines = getWrappedLines(mCtx, urduText, maxW);

        var arabicH = arabicLines.length * 50;
        var englishH = englishLines.length * 32;
        var urduH = urduLines.length * 32;

        var totalH = yPadding + arabicH + sectionGap + englishH + sectionGap + urduH + sectionGap + footerH;
        totalH = Math.max(totalH, 500);

        // Set final canvas size
        canvas.width = W;
        canvas.height = totalH;

        // Draw gradient background
        var gradient = ctx.createLinearGradient(0, 0, W, totalH);
        gradient.addColorStop(0, t.bg1);
        gradient.addColorStop(1, t.bg2);
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, W, totalH);

        // Decorative pattern
        ctx.strokeStyle = "rgba(255,255,255,0.1)";
        ctx.lineWidth = 1;
        for (var i = 0; i < 20; i++) {
            ctx.beginPath();
            ctx.arc(Math.random() * W, Math.random() * totalH, Math.random() * 100 + 50, 0, Math.PI * 2);
            ctx.stroke();
        }

        var curY = yPadding;

        // Arabic text
        ctx.fillStyle = t.text;
        ctx.textAlign = "center";
        ctx.font = "36px Muhammadi, Arial";
        drawLines(ctx, arabicLines, W / 2, curY, 50);
        curY += arabicH + sectionGap;

        // English translation
        ctx.font = "italic 22px Calibri, Arial";
        ctx.fillStyle = t.text;
        drawLines(ctx, englishLines, W / 2, curY, 32);
        curY += englishH + sectionGap;

        // Urdu translation
        ctx.font = "22px Jameel Noori Nastaleeq, Arial";
        ctx.fillStyle = t.text;
        ctx.textAlign = "center";
        drawLines(ctx, urduLines, W / 2, curY, 32);
        curY += urduH + sectionGap;

        // Reference
        ctx.font = "bold 24px Calibri, Arial";
        ctx.fillStyle = t.text;
        ctx.fillText("- " + imageData.suraName + " " + imageData.sura + ":" + imageData.aya, W / 2, curY);
        curY += 30;

        // Decorative line
        ctx.strokeStyle = "rgba(255,255,255,0.5)";
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(200, curY);
        ctx.lineTo(600, curY);
        ctx.stroke();
        curY += 20;

        // Website
        ctx.font = "14px Calibri, Arial";
        ctx.fillStyle = "rgba(255,255,255,0.7)";
        ctx.fillText("Al-Quran Simple  -  Alahazrat.net", W / 2, curY);
    }

    function getWrappedLines(ctx, text, maxWidth) {
        var words = text.split(" ");
        var line = "";
        var lines = [];

        for (var n = 0; n < words.length; n++) {
            var testLine = line + words[n] + " ";
            var metrics = ctx.measureText(testLine);
            if (metrics.width > maxWidth && n > 0) {
                lines.push(line);
                line = words[n] + " ";
            } else {
                line = testLine;
            }
        }
        lines.push(line);
        return lines;
    }

    function drawLines(ctx, lines, x, y, lineHeight) {
        for (var i = 0; i < lines.length; i++) {
            ctx.fillText(lines[i], x, y + (i * lineHeight));
        }
    }

    function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
        var lines = getWrappedLines(ctx, text, maxWidth);
        drawLines(ctx, lines, x, y, lineHeight);
    }

    window.downloadImage = function() {
        var canvas = document.getElementById("verse-canvas");
        var link = document.createElement("a");
        link.download = "quran-" + imageData.sura + "-" + imageData.aya + ".png";
        link.href = canvas.toDataURL("image/png");
        link.click();
        showToast("Image downloaded!");
    };

    window.closeImageModal = function() {
        $("#image-modal").removeClass("active");
    };

    $(document).on("click", "#image-modal", function(e) {
        if ($(e.target).is("#image-modal")) closeImageModal();
    });

    // ============ QR CODE GENERATOR (Pure JS) ============
    var qrData = {};

    // QR Code Generator - Simplified implementation
    var QRCode = (function() {
        var PAD0 = 0xEC, PAD1 = 0x11;

        function QRCode(data, size) {
            this.size = size || 200;
            this.data = data;
        }

        QRCode.prototype.generate = function(canvas) {
            var qr = this.createQR(this.data);
            this.draw(canvas, qr);
        };

        QRCode.prototype.createQR = function(data) {
            var size = 21; // Version 1
            var modules = [];
            for (var i = 0; i < size; i++) {
                modules[i] = [];
                for (var j = 0; j < size; j++) {
                    modules[i][j] = false;
                }
            }

            // Add finder patterns
            this.addFinderPattern(modules, 0, 0);
            this.addFinderPattern(modules, size - 7, 0);
            this.addFinderPattern(modules, 0, size - 7);

            // Add timing patterns
            for (var i = 8; i < size - 8; i++) {
                modules[6][i] = modules[i][6] = (i % 2 === 0);
            }

            // Encode data (simplified)
            var bits = this.encodeData(data);
            var idx = 0;
            for (var col = size - 1; col >= 1; col -= 2) {
                if (col === 6) col = 5;
                for (var row = 0; row < size; row++) {
                    for (var c = 0; c < 2; c++) {
                        var x = col - c;
                        if (modules[row][x] === false && idx < bits.length) {
                            modules[row][x] = bits[idx] === "1";
                            idx++;
                        }
                    }
                }
            }

            return { modules: modules, size: size };
        };

        QRCode.prototype.addFinderPattern = function(modules, row, col) {
            for (var r = -1; r <= 7; r++) {
                for (var c = -1; c <= 7; c++) {
                    if (row + r >= 0 && row + r < modules.length && col + c >= 0 && col + c < modules.length) {
                        if ((r >= 0 && r <= 6 && (c === 0 || c === 6)) ||
                            (c >= 0 && c <= 6 && (r === 0 || r === 6)) ||
                            (r >= 2 && r <= 4 && c >= 2 && c <= 4)) {
                            modules[row + r][col + c] = true;
                        } else {
                            modules[row + r][col + c] = false;
                        }
                    }
                }
            }
        };

        QRCode.prototype.encodeData = function(data) {
            var bits = "0100"; // Byte mode
            bits += this.pad(data.length.toString(2), 8);
            for (var i = 0; i < data.length; i++) {
                bits += this.pad(data.charCodeAt(i).toString(2), 8);
            }
            while (bits.length < 152) bits += "0";
            while (bits.length < 208) {
                bits += this.pad(PAD0.toString(2), 8);
                if (bits.length < 208) bits += this.pad(PAD1.toString(2), 8);
            }
            return bits;
        };

        QRCode.prototype.pad = function(str, len) {
            while (str.length < len) str = "0" + str;
            return str;
        };

        QRCode.prototype.draw = function(canvas, qr) {
            var ctx = canvas.getContext("2d");
            var cellSize = Math.floor(this.size / qr.size);
            canvas.width = canvas.height = cellSize * qr.size;

            ctx.fillStyle = "#ffffff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = "#000000";
            for (var row = 0; row < qr.size; row++) {
                for (var col = 0; col < qr.size; col++) {
                    if (qr.modules[row][col]) {
                        ctx.fillRect(col * cellSize, row * cellSize, cellSize, cellSize);
                    }
                }
            }
        };

        return QRCode;
    })();

    window.openQRGenerator = function(sura, aya) {
        var baseUrl = window.location.origin + window.location.pathname;
        var verseUrl = baseUrl + "?sura=" + sura + "#aya-" + sura + "-" + aya;
        qrData = { sura: sura, aya: aya, url: verseUrl };

        $("#qr-link").text(verseUrl);
        $("#qr-modal").addClass("active");

        var canvas = document.getElementById("qr-canvas");
        var qr = new QRCode(verseUrl, 200);
        qr.generate(canvas);
    };

    window.downloadQR = function() {
        var canvas = document.getElementById("qr-canvas");
        var link = document.createElement("a");
        link.download = "qr-quran-" + qrData.sura + "-" + qrData.aya + ".png";
        link.href = canvas.toDataURL("image/png");
        link.click();
        showToast("QR Code downloaded!");
    };

    window.closeQRModal = function() {
        $("#qr-modal").removeClass("active");
    };

    $(document).on("click", "#qr-modal", function(e) {
        if ($(e.target).is("#qr-modal")) closeQRModal();
    });

    // ============ READING CHALLENGES ============
    var challengeGoals = {
        easy: { verses: 10, name: "10 Verses", desc: "Read 10 verses daily" },
        medium: { verses: 30, name: "1 Page", desc: "Read about 1 page daily" },
        hard: { verses: 100, name: "3 Pages", desc: "Read about 3 pages daily" },
        intense: { verses: 200, name: "1 Juz", desc: "Read approximately 1 Juz weekly" }
    };

    function getChallengeData() {
        try {
            return JSON.parse(localStorage.getItem("quran_challenge") || "{}");
        } catch(e) { return {}; }
    }

    function saveChallengeData(data) {
        localStorage.setItem("quran_challenge", JSON.stringify(data));
    }

    function initChallenge() {
        var data = getChallengeData();
        if (data.goal) {
            updateChallengeWidget();
            $("#challenge-widget").addClass("active");
        }
        trackReading();
    }

    function trackReading() {
        var data = getChallengeData();
        if (!data.goal) return;

        var today = new Date().toDateString();
        if (data.lastDate !== today) {
            // Check streak
            var yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            if (data.lastDate === yesterday.toDateString()) {
                data.streak = (data.streak || 0) + 1;
            } else if (data.lastDate !== today) {
                data.streak = 1;
            }
            data.todayVerses = 0;
            data.lastDate = today;
            saveChallengeData(data);
        }

        // Track scroll reading
        var scrollTimer;
        $(window).on("scroll.challenge", function() {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function() {
                var visibleAyas = $(".aya:visible").filter(function() {
                    var rect = this.getBoundingClientRect();
                    return rect.top >= 0 && rect.top <= window.innerHeight;
                });

                if (visibleAyas.length > 0) {
                    var data = getChallengeData();
                    var prevCount = data.todayVerses || 0;
                    data.todayVerses = Math.max(prevCount, prevCount + 1);
                    data.totalVerses = (data.totalVerses || 0) + 1;
                    saveChallengeData(data);
                    updateChallengeWidget();
                }
            }, 2000);
        });
    }

    function updateChallengeWidget() {
        var data = getChallengeData();
        if (!data.goal) return;

        var goal = challengeGoals[data.goal];
        var progress = Math.min(100, Math.round((data.todayVerses / goal.verses) * 100));

        $("#streak-count").text(data.streak || 1);
        $("#progress-fill").css("width", progress + "%").text(progress + "%");
        $("#today-verses").text(data.todayVerses || 0);
        $("#total-verses").text(data.totalVerses || 0);
        $("#goal-verses").text(goal.verses);

        // Check if goal completed
        if (progress >= 100 && !data.completedToday) {
            data.completedToday = true;
            saveChallengeData(data);
            showToast("Daily goal completed! Great job!");
            sendNotification("Daily Goal Achieved!", "You have completed your Quran reading goal for today.");
        }
    }

    window.openChallengeModal = function() {
        $("#challenge-modal").addClass("active");
        var data = getChallengeData();
        if (data.goal) {
            $(".goal-option").removeClass("selected");
            $(".goal-option[data-goal='" + data.goal + "']").addClass("selected");
        }
    };

    window.selectGoal = function(goal) {
        $(".goal-option").removeClass("selected");
        $(".goal-option[data-goal='" + goal + "']").addClass("selected");
    };

    window.saveChallenge = function() {
        var selectedGoal = $(".goal-option.selected").data("goal");
        if (!selectedGoal) {
            showToast("Please select a goal");
            return;
        }

        var data = getChallengeData();
        data.goal = selectedGoal;
        data.todayVerses = data.todayVerses || 0;
        data.totalVerses = data.totalVerses || 0;
        data.streak = data.streak || 1;
        data.lastDate = new Date().toDateString();
        data.notifications = $("#notification-toggle").is(":checked");

        saveChallengeData(data);
        closeChallengeModal();
        $("#challenge-widget").addClass("active");
        updateChallengeWidget();
        showToast("Challenge started!");

        if (data.notifications) {
            requestNotificationPermission();
        }
    };

    window.closeChallengeModal = function() {
        $("#challenge-modal").removeClass("active");
    };

    function requestNotificationPermission() {
        if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }
    }

    function sendNotification(title, body) {
        if ("Notification" in window && Notification.permission === "granted") {
            new Notification(title, { body: body, icon: "/favicon.ico" });
        }
    }

    $(document).on("click", "#challenge-modal", function(e) {
        if ($(e.target).is("#challenge-modal")) closeChallengeModal();
    });

})(jQuery);
