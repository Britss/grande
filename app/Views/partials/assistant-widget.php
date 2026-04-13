<?php
$assistantContact = \App\Support\PublicContent::shared()['contact'];
$assistantConfig = [
    'names' => [
        'website' => 'GrandeGo',
        'shop' => 'Grande',
    ],
    'icons' => [
        'coffee' => url('public/icons/coffee.png'),
        'close' => url('public/icons/close.png'),
        'send' => url('public/icons/send.png'),
        'menu' => url('public/icons/menu.png'),
        'clock' => url('public/icons/clock.png'),
        'reserve' => url('public/icons/timetable.png'),
        'location' => url('public/icons/pin.png'),
        'contact' => url('public/icons/telephone.png'),
        'order' => url('public/icons/shopping-cart.png'),
    ],
    'links' => [
        'menu' => url('menu'),
        'reserve' => url('reserve'),
        'feedback' => url('feedback'),
        'cart' => url('cart'),
    ],
    'contact' => [
        'phone' => $assistantContact['phone'] ?? '',
        'email' => $assistantContact['email'] ?? '',
        'address' => $assistantContact['address'] ?? '',
    ],
];
?>

<script>
    window.GRANDE_ASSISTANT = <?= json_encode($assistantConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
</script>

<section class="assistant-widget" data-assistant-widget aria-label="GrandeGo assistant">
    <button
        class="assistant-toggle"
        type="button"
        aria-label="Open GrandeGo assistant"
        aria-controls="assistant-window"
        aria-expanded="false"
        data-assistant-toggle
    >
        <img src="<?= e(url('public/icons/coffee.png')) ?>" width="32" height="32" alt="" aria-hidden="true">
        <span class="assistant-badge" data-assistant-badge>1</span>
    </button>

    <div
        class="assistant-window"
        id="assistant-window"
        role="dialog"
        aria-modal="false"
        aria-hidden="true"
        aria-labelledby="assistant-title"
        data-assistant-window
        hidden
    >
        <div class="assistant-header">
            <div>
                <h2 id="assistant-title">GrandeGo Assistant</h2>
                <p>For Grande coffee shop</p>
            </div>
            <button class="assistant-close" type="button" aria-label="Close assistant" data-assistant-close>
                <img src="<?= e(url('public/icons/close.png')) ?>" width="18" height="18" alt="" aria-hidden="true">
            </button>
        </div>

        <div class="assistant-body" data-assistant-body aria-live="polite"></div>
        <div class="assistant-quick-replies" data-assistant-quick-replies hidden></div>

        <form class="assistant-compose" data-assistant-form>
            <label class="sr-only" for="assistant-input">Message</label>
            <input
                id="assistant-input"
                class="assistant-input"
                type="text"
                placeholder="Type your message..."
                autocomplete="off"
                data-assistant-input
            >
            <button class="assistant-send" type="submit" aria-label="Send message">
                <img src="<?= e(url('public/icons/send.png')) ?>" width="18" height="18" alt="" aria-hidden="true">
            </button>
        </form>
    </div>
</section>
