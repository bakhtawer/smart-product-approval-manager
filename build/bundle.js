(function(){
    // Minimal admin UI bundle (vanilla JS)
    function el(tag, attrs, ...children) {
        var node = document.createElement(tag);
        if (attrs) Object.keys(attrs).forEach(k => node.setAttribute(k, attrs[k]));
        children.forEach(c => {
            if (typeof c === 'string') node.appendChild(document.createTextNode(c));
            else if (c) node.appendChild(c);
        });
        return node;
    }

    async function api(path, options) {
        options = options || {};
        options.headers = options.headers || {};
        options.headers['X-WP-Nonce'] = (window.spamApi && window.spamApi.nonce) ? window.spamApi.nonce : '';
        const res = await fetch((window.spamApi && window.spamApi.root ? window.spamApi.root : '/wp-json/spam/v1/') + path, options);
        return res.json();
    }

    function createCard(p) {
        var card = el('div', {class:'spam-card'});
        var title = el('div', {class:'spam-title'}, p.name || ('Product #' + p.id));
        var btns = el('div', {class:'spam-actions'});
        var approve = el('button', {class:'spam-approve'}, 'Approve');
        var reject = el('button', {class:'spam-reject'}, 'Reject');
        approve.addEventListener('click', async function(){
            approve.disabled = true;
            await api('approve/' + p.id, { method: 'POST' });
            loadProducts();
        });
        reject.addEventListener('click', async function(){
            reject.disabled = true;
            await api('reject/' + p.id, { method: 'POST' });
            loadProducts();
        });
        btns.appendChild(approve);
        btns.appendChild(reject);
        card.appendChild(title);
        card.appendChild(btns);
        return card;
    }

    async function loadProducts() {
        var root = document.getElementById('spam-root');
        if (!root) return;
        root.innerHTML = '<p>Loading...</p>';
        try {
            var products = await api('pending-products');
            root.innerHTML = '';
            var list = el('div', {id:'spam-list'});
            products.forEach(p => {
                list.appendChild(createCard(p));
            });
            root.appendChild(list);

            // show logs
            var logBtn = el('button', {id:'spam-show-logs'}, 'Show recent logs (console)');
            logBtn.addEventListener('click', async function(){
                const res = await fetch((window.spamApi && window.spamApi.root ? window.spamApi.root : '/wp-json/spam/v1/') + 'pending-products', { headers: { 'X-WP-Nonce': window.spamApi && window.spamApi.nonce ? window.spamApi.nonce : '' }});
                console.log('Pending (raw):', await res.json());
                alert('Logs available in browser console (DB logs require DB access).');
            });
            root.appendChild(logBtn);

        } catch (e) {
            root.innerHTML = '<p>Error loading products. Check console.</p>';
            console.error(e);
        }
    }

    document.addEventListener('DOMContentLoaded', function(){
        loadProducts();
    });
})();
