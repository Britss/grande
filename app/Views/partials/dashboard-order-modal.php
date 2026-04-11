<div class="dashboard-modal" id="dashboard-order-modal" aria-hidden="true">
    <div class="dashboard-modal__backdrop" data-dashboard-modal-close></div>
    <div class="dashboard-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="dashboard-order-modal-title">
        <button class="dashboard-modal__close" type="button" aria-label="Close order details" data-dashboard-modal-close>&times;</button>
        <div class="dashboard-modal__header">
            <p class="eyebrow">Order Snapshot</p>
            <h2 id="dashboard-order-modal-title">Order Details</h2>
            <p class="dashboard-modal__lead" id="dashboard-order-modal-subtitle">Review the current order timeline, statuses, and purchased items.</p>
        </div>
        <div class="dashboard-modal__summary">
            <div>
                <span class="dashboard-modal__label">Order Number</span>
                <p class="dashboard-modal__value" id="dashboard-order-number">-</p>
            </div>
            <div class="dashboard-modal__badges">
                <span class="status-pill" id="dashboard-order-status-badge">Pending</span>
                <span class="status-pill" id="dashboard-order-payment-badge">Pending</span>
            </div>
        </div>
        <div class="dashboard-modal__meta" id="dashboard-order-meta">
            <div class="dashboard-modal__meta-item">
                <span class="dashboard-modal__label">Placed On</span>
                <p id="dashboard-order-date">-</p>
            </div>
            <div class="dashboard-modal__meta-item">
                <span class="dashboard-modal__label">Order Type</span>
                <p id="dashboard-order-flow">-</p>
            </div>
            <div class="dashboard-modal__meta-item">
                <span class="dashboard-modal__label">Total Amount</span>
                <p id="dashboard-order-total">-</p>
            </div>
        </div>
        <div class="dashboard-modal__panel">
            <div class="dashboard-modal__panel-header">
                <div>
                    <span class="dashboard-modal__label">Items</span>
                    <h3>Purchased Items</h3>
                </div>
                <span class="dashboard-modal__count" id="dashboard-order-item-count">0 items</span>
            </div>
            <div class="dashboard-modal__items" id="dashboard-order-items"></div>
        </div>
        <div class="dashboard-modal__footer">
            <a class="button button-secondary" href="#" target="_blank" rel="noreferrer" id="dashboard-order-receipt-link" hidden>Open Receipt</a>
            <button class="button button-primary" type="button" data-dashboard-modal-close>Close</button>
        </div>
    </div>
</div>
