/**
 * EcountValidator — 客製化全站表單驗證
 *
 * 使用方式：
 *   <input data-rules="required|email" data-label="Email">
 *   <input data-rules="required|min:8|max:128" data-label="密碼">
 *   <input data-rules="required|same:#password" data-label="確認密碼">
 *   <input data-rules="phone" data-label="電話">
 *   <input data-message-required="請輸入姓名">   (per-rule 自訂訊息)
 *   <input data-message="統一自訂錯誤訊息">
 *   <form data-ev-skip>                          (跳過此表單)
 *   <form data-validate>                         (GET 表單也啟用)
 *
 * 支援規則：
 *   required, email, min:N, max:N, numeric, min_value:N, max_value:N,
 *   phone, taiwan_id, same:#selector, date, url, regex:pattern
 *
 * 自動偵測 HTML 屬性：required, type="email", minlength, maxlength, min, max
 */
(function (window) {
    'use strict';

    /* ─── 規則定義 ─── */
    var RULES = {
        required:  function (v) { return v.trim() !== ''; },
        email:     function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()); },
        min:       function (v, n) { return v.trim().length >= parseInt(n, 10); },
        max:       function (v, n) { return v.trim().length <= parseInt(n, 10); },
        numeric:   function (v) { return v.trim() !== '' && !isNaN(Number(v.trim())); },
        min_value: function (v, n) { return parseFloat(v) >= parseFloat(n); },
        max_value: function (v, n) { return parseFloat(v) <= parseFloat(n); },
        phone:     function (v) {
            return /^(\+?886[-\s]?|0)([2-9]\d{7,8}|9\d{8})$/.test(v.replace(/[-\s()]/g, ''));
        },
        taiwan_id: function (v) { return validateTaiwanId(v); },
        date:      function (v) { return v.trim() !== '' && !isNaN(Date.parse(v)); },
        url:       function (v) { try { new URL(v); return true; } catch (e) { return false; } },
        same:      function (v, sel, form) {
            var target = form ? form.querySelector(sel) : document.querySelector(sel);
            return target ? v === target.value : false;
        },
        regex:     function (v, pattern) {
            try { return new RegExp(pattern).test(v); } catch (e) { return true; }
        },
    };

    /* ─── 預設錯誤訊息 ─── */
    var MESSAGES = {
        required:  function (l)    { return l + ' 為必填'; },
        email:     function ()     { return '請輸入有效的 Email 格式（例：name@example.com）'; },
        min:       function (l, n) { return l + ' 至少需要 ' + n + ' 個字元'; },
        max:       function (l, n) { return l + ' 最多 ' + n + ' 個字元'; },
        numeric:   function (l)    { return l + ' 必須為數字'; },
        min_value: function (l, n) { return l + ' 最小值為 ' + n; },
        max_value: function (l, n) { return l + ' 最大值為 ' + n; },
        phone:     function ()     { return '請輸入有效的電話號碼（例：0912-345-678）'; },
        taiwan_id: function ()     { return '請輸入有效的身分證字號'; },
        date:      function (l)    { return l + ' 日期格式不正確'; },
        url:       function ()     { return '請輸入有效的網址（需包含 http:// 或 https://）'; },
        same:      function (l)    { return l + ' 與確認欄位不一致'; },
        regex:     function (l)    { return l + ' 格式不符'; },
    };

    /* ─── 台灣身分證字號驗證 ─── */
    function validateTaiwanId(id) {
        if (typeof id !== 'string') return false;
        id = id.toUpperCase().trim();
        if (!/^[A-Z][12]\d{8}$/.test(id)) return false;
        var letters = 'ABCDEFGHJKLMNPQRSTUVXYWZIO';
        var lv = letters.indexOf(id[0]) + 10;
        var sum = Math.floor(lv / 10) + (lv % 10) * 9;
        for (var i = 1; i <= 8; i++) sum += parseInt(id[i], 10) * (9 - i);
        sum += parseInt(id[9], 10);
        return sum % 10 === 0;
    }

    /* ─── DOM 輔助 ─── */
    function getWrapper(el) {
        return el.closest('.mb-2,.mb-3,.mb-4,.mb-5,.mb-6,.form-group,.field-group') || el.parentElement;
    }

    function getLabel(el) {
        if (el.dataset.label) return el.dataset.label;
        var wrapper = getWrapper(el);
        if (wrapper) {
            var lbl = wrapper.querySelector('label');
            if (lbl) return lbl.textContent.trim().replace(/\s*[*＊]\s*$/, '').trim();
        }
        return el.placeholder || el.getAttribute('name') || '此欄位';
    }

    function escHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    /* ─── 狀態清除 / 標記 ─── */
    function clearFieldState(el) {
        var wrapper = getWrapper(el);
        var err = wrapper.querySelector('.ev-error');
        if (err) err.remove();
        el.classList.remove('ev-invalid', 'ev-valid', 'border-red-500', 'ring-1', 'ring-red-300', 'border-green-500');
    }

    function markInvalid(el, msg) {
        clearFieldState(el);
        el.classList.add('ev-invalid', 'border-red-500', 'ring-1', 'ring-red-300');
        var wrapper = getWrapper(el);
        var p = document.createElement('p');
        p.className = 'ev-error text-xs text-red-500 mt-1 flex items-center gap-1';
        p.innerHTML =
            '<svg class="inline w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">' +
            '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>' +
            escHtml(msg);
        wrapper.appendChild(p);
    }

    function markValid(el) {
        clearFieldState(el);
        el.classList.add('ev-valid', 'border-green-500');
    }

    /* ─── 取得欄位規則列表 ─── */
    function getRules(el) {
        var rules = [];

        // From data-rules
        if (el.dataset.rules) {
            el.dataset.rules.split('|').forEach(function (r) {
                var trimmed = r.trim();
                if (trimmed) rules.push(trimmed);
            });
        }

        // Auto-detect from HTML attributes
        if (el.hasAttribute('required') && rules.indexOf('required') === -1) {
            rules.unshift('required');
        }
        if (el.type === 'email' && !rules.some(function (r) { return r === 'email'; })) {
            rules.push('email');
        }
        if (el.getAttribute('minlength') && !rules.some(function (r) { return r.startsWith('min:'); })) {
            rules.push('min:' + el.getAttribute('minlength'));
        }
        if (el.getAttribute('maxlength') && !rules.some(function (r) { return r.startsWith('max:'); })) {
            rules.push('max:' + el.getAttribute('maxlength'));
        }
        if (el.type === 'number') {
            if (el.getAttribute('min') && !rules.some(function (r) { return r.startsWith('min_value:'); })) {
                rules.push('min_value:' + el.getAttribute('min'));
            }
            if (el.getAttribute('max') && !rules.some(function (r) { return r.startsWith('max_value:'); })) {
                rules.push('max_value:' + el.getAttribute('max'));
            }
        }

        return rules;
    }

    /* ─── 單欄位驗證 ─── */
    function validateField(el, form) {
        if (!el || el.type === 'hidden' || el.type === 'submit' || el.type === 'button' ||
            el.type === 'image' || el.type === 'radio') return true;

        var rules = getRules(el);
        if (rules.length === 0) { clearFieldState(el); return true; }

        var label = getLabel(el);
        var value = el.value || '';

        // Checkbox: required = must be checked
        if (el.type === 'checkbox') {
            if (rules.indexOf('required') !== -1 && !el.checked) {
                markInvalid(el, el.dataset.messageRequired || MESSAGES.required(label));
                return false;
            }
            markValid(el);
            return true;
        }

        for (var i = 0; i < rules.length; i++) {
            var ruleStr  = rules[i];
            var colonIdx = ruleStr.indexOf(':');
            var ruleName, ruleParam;

            if (colonIdx === -1) {
                ruleName  = ruleStr;
                ruleParam = '';
            } else {
                ruleName  = ruleStr.slice(0, colonIdx);
                ruleParam = ruleStr.slice(colonIdx + 1);
            }

            var validator = RULES[ruleName];
            if (!validator) continue;

            // Non-required rules are skipped for empty optional fields
            if (ruleName !== 'required' && value.trim() === '') continue;

            var valid = (ruleName === 'same')
                ? validator(value, ruleParam, form)
                : (ruleParam !== '' ? validator(value, ruleParam) : validator(value));

            if (!valid) {
                // data-message-required → messageRequired, data-message-min-value → messageMinValue
                var camelKey = 'message' + ruleName
                    .split('_')
                    .map(function (w) { return w.charAt(0).toUpperCase() + w.slice(1); })
                    .join('');
                var customMsg = el.dataset[camelKey] || el.dataset.message;
                var errMsg    = customMsg || (MESSAGES[ruleName] ? MESSAGES[ruleName](label, ruleParam) : label + ' 驗證失敗');
                markInvalid(el, errMsg);
                return false;
            }
        }

        markValid(el);
        return true;
    }

    /* ─── Radio 群組驗證 ─── */
    function validateRadioGroup(name, form) {
        var radios = Array.prototype.slice.call(
            form.querySelectorAll('input[type=radio][name="' + CSS.escape(name) + '"]')
        );
        if (!radios.length) return true;

        var isRequired = radios.some(function (r) {
            return r.hasAttribute('required') || (r.dataset.rules || '').indexOf('required') !== -1;
        });
        if (!isRequired) return true;

        var wrapper = getWrapper(radios[0]);
        var oldErr  = wrapper.querySelector('.ev-error');
        if (oldErr) oldErr.remove();

        if (!radios.some(function (r) { return r.checked; })) {
            var p = document.createElement('p');
            p.className = 'ev-error text-xs text-red-500 mt-1';
            p.textContent = '請選擇一個選項';
            wrapper.appendChild(p);
            return false;
        }
        return true;
    }

    /* ─── 整個表單驗證（submit 用）─── */
    function validateForm(form) {
        var errors    = 0;
        var firstError = null;

        var fields = Array.prototype.slice.call(form.querySelectorAll('input, select, textarea'));
        fields.forEach(function (el) {
            if (el.type === 'radio') return;
            if (!validateField(el, form)) {
                errors++;
                if (!firstError) firstError = el;
            }
        });

        // Radio groups
        var radioNames = {};
        form.querySelectorAll('input[type=radio]').forEach(function (r) {
            radioNames[r.name] = true;
        });
        Object.keys(radioNames).forEach(function (name) {
            if (!validateRadioGroup(name, form)) {
                errors++;
                if (!firstError) firstError = form.querySelector('input[type=radio][name]');
            }
        });

        if (errors > 0 && firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(function () { try { firstError.focus(); } catch (e) {} }, 250);
        }

        return errors === 0;
    }

    /* ─── 初始化單一表單 ─── */
    function initForm(form) {
        form.setAttribute('novalidate', '');

        var fields = Array.prototype.slice.call(form.querySelectorAll('input, select, textarea'));
        fields.forEach(function (el) {
            if (el.type === 'radio' || el.type === 'hidden' || el.type === 'submit' || el.type === 'button') return;

            el.addEventListener('blur', function () {
                validateField(el, form);
            });
            el.addEventListener('input', function () {
                if (el.classList.contains('ev-invalid')) validateField(el, form);
            });
            el.addEventListener('change', function () {
                validateField(el, form);
            });
        });

        form.addEventListener('submit', function (e) {
            if (!validateForm(form)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }

    /* ─── 全站初始化 ─── */
    function init() {
        Array.prototype.slice.call(document.querySelectorAll('form')).forEach(function (form) {
            if (form.method.toLowerCase() !== 'post' && !form.hasAttribute('data-validate')) return;
            if (form.hasAttribute('data-ev-skip') || form.dataset.novalidate === 'true') return;
            initForm(form);
        });
    }

    document.addEventListener('DOMContentLoaded', init);

    // Public API
    window.EcountValidator = {
        init:            init,
        initForm:        initForm,
        validateForm:    validateForm,
        validateField:   validateField,
        markInvalid:     markInvalid,
        markValid:       markValid,
        clearFieldState: clearFieldState,
    };

})(window);
