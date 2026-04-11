<div class="dashboard-modal" id="dashboard-reservation-modal" aria-hidden="true">
    <div class="dashboard-modal__backdrop" data-dashboard-modal-close></div>
    <div class="dashboard-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="dashboard-reservation-modal-title">
        <button class="dashboard-modal__close" type="button" aria-label="Close reservation details" data-dashboard-modal-close>&times;</button>
        <div class="dashboard-modal__header">
            <p class="eyebrow">Reservation Snapshot</p>
            <h2 id="dashboard-reservation-modal-title">Reservation Details</h2>
            <p class="dashboard-modal__lead">Review the reservation schedule, contact details, and linked reservation orders.</p>
        </div>
        <div class="dashboard-modal__summary">
            <div>
                <span class="dashboard-modal__label">Reservation</span>
                <p class="dashboard-modal__value" id="dashboard-reservation-number">-</p>
            </div>
            <div class="dashboard-modal__badges">
                <span class="status-pill" id="dashboard-reservation-status-badge">Pending</span>
            </div>
        </div>
        <div class="dashboard-modal__meta">
            <div class="dashboard-modal__meta-item">
                <span class="dashboard-modal__label">Guest Name</span>
                <p id="dashboard-reservation-name">-</p>
            </div>
            <div class="dashboard-modal__meta-item">
                <span class="dashboard-modal__label">Schedule</span>
                <p id="dashboard-reservation-schedule">-</p>
            </div>
            <div class="dashboard-modal__meta-item">
                <span class="dashboard-modal__label">Guests</span>
                <p id="dashboard-reservation-guests">-</p>
            </div>
            <div class="dashboard-modal__meta-item">
                <span class="dashboard-modal__label">Email</span>
                <p id="dashboard-reservation-email">-</p>
            </div>
            <div class="dashboard-modal__meta-item">
                <span class="dashboard-modal__label">Phone</span>
                <p id="dashboard-reservation-phone">-</p>
            </div>
            <div class="dashboard-modal__meta-item">
                <span class="dashboard-modal__label">Booked On</span>
                <p id="dashboard-reservation-created-at">-</p>
            </div>
        </div>
        <div class="dashboard-modal__panel">
            <div class="dashboard-modal__panel-header">
                <div>
                    <span class="dashboard-modal__label">Linked Orders</span>
                    <h3>Reservation Orders</h3>
                </div>
                <span class="dashboard-modal__count" id="dashboard-reservation-order-count">0 orders</span>
            </div>
            <div class="dashboard-modal__items" id="dashboard-reservation-orders"></div>
        </div>
        <div class="dashboard-modal__footer">
            <button class="button button-primary" type="button" data-dashboard-modal-close>Close</button>
        </div>
    </div>
</div>
