@php
    $__systemUpdate = \App\Models\SystemUpdate::latestUpdate();
    $__systemUpdatesModalOn = \App\Models\SystemSetting::getValue('system_updates_modal_enabled', 'on') === 'on';
    $__showSystemUpdateModal = session('show_system_update') && $__systemUpdatesModalOn && $__systemUpdate;
@endphp

@if($__showSystemUpdateModal)
    @php session()->forget('show_system_update'); @endphp

    <div id="systemUpdateModalOverlay" class="system-update-modal-overlay">
        <div class="system-update-modal">
            <button type="button" class="system-update-modal-x" onclick="closeSystemUpdateModal()" aria-label="Close">&times;</button>

            <div class="system-update-modal-header">
                <div class="system-update-modal-icon">🎉</div>
                <h3>What's New in UCC-ERS</h3>
                <span class="system-update-version-badge">Version {{ $__systemUpdate->version }}</span>
            </div>

            <div class="system-update-modal-body">
                <ul class="system-update-list">
                    @foreach($__systemUpdate->updates as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="system-update-modal-footer">
                <button type="button" class="system-update-close-btn" onclick="closeSystemUpdateModal()">Got it, thanks!</button>
            </div>
        </div>
    </div>

    <style>
        .system-update-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(10, 61, 31, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5000;
            padding: 20px;
            animation: systemUpdateFadeIn 0.25s ease;
        }

        @keyframes systemUpdateFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .system-update-modal {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 440px;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 50px rgba(0,0,0,0.25);
            position: relative;
            animation: systemUpdateSlideUp 0.3s ease;
        }

        @keyframes systemUpdateSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .system-update-modal-x {
            position: absolute;
            top: 14px;
            right: 16px;
            background: none;
            border: none;
            font-size: 22px;
            line-height: 1;
            color: rgba(255,255,255,0.85);
            cursor: pointer;
            z-index: 1;
        }

        .system-update-modal-header {
            background: linear-gradient(135deg, #1a7a3e, #2db84f);
            color: #fff;
            padding: 30px 25px 25px;
            text-align: center;
            border-radius: 20px 20px 0 0;
        }

        .system-update-modal-icon {
            font-size: 38px;
            margin-bottom: 8px;
        }

        .system-update-modal-header h3 {
            margin: 0 0 10px;
            font-size: 19px;
            font-weight: 700;
        }

        .system-update-version-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .system-update-modal-body {
            padding: 22px 25px;
        }

        .system-update-list {
            margin: 0;
            padding-left: 20px;
            color: #33413a;
            font-size: 14px;
            line-height: 1.7;
        }

        .system-update-list li {
            margin-bottom: 8px;
        }

        .system-update-modal-footer {
            padding: 0 25px 25px;
        }

        .system-update-close-btn {
            width: 100%;
            background: linear-gradient(135deg, #1a7a3e, #2db84f);
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .system-update-close-btn:hover {
            opacity: 0.92;
        }

        @media (max-width: 480px) {
            .system-update-modal {
                max-width: 100%;
            }
        }
    </style>

    <script>
        function closeSystemUpdateModal() {
            const overlay = document.getElementById('systemUpdateModalOverlay');
            if (overlay) {
                overlay.remove();
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeSystemUpdateModal();
            }
        });
    </script>
@endif