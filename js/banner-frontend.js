/**
 * フロントエンド用バナー制御スクリプト
 */
document.addEventListener('DOMContentLoaded', () => {
    const banner = document.querySelector('.modern-banner');
    if (!banner) return;

    /**
     * バナーの表示制御
     */
    const showBanner = () => {
        // スクロール位置の取得
        const scrollPosition = window.scrollY || window.pageYOffset;
        const viewportHeight = window.innerHeight;
        
        // 画面の20%以上スクロールしたらバナーを表示
        if (scrollPosition > viewportHeight * 0.2) {
            banner.classList.add('is-visible');
            // スクロールイベントを解除（一度表示したら監視を止める）
            window.removeEventListener('scroll', handleScroll);
        }
    };

    /**
     * スクロールハンドラ（パフォーマンス最適化）
     */
    const handleScroll = () => {
        if (scrollTimeout) {
            window.cancelAnimationFrame(scrollTimeout);
        }
        scrollTimeout = window.requestAnimationFrame(showBanner);
    };

    /**
     * バナーの非表示処理
     */
    const hideBanner = () => {
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(100%)';
        
        // アニメーション完了後に要素を削除
        setTimeout(() => {
            banner.remove();
        }, 300);

        // 非表示状態を記憶（24時間）
        const expires = new Date();
        expires.setTime(expires.getTime() + (24 * 60 * 60 * 1000));
        document.cookie = 'modern_banner_closed=1; expires=' + expires.toUTCString() + '; path=/';
    };

    // 閉じるボタンのイベント設定
    const closeButton = banner.querySelector('.modern-banner-close');
    if (closeButton) {
        closeButton.addEventListener('click', (e) => {
            e.preventDefault();
            hideBanner();
        });
    }

    // Cookieチェック
    const isBannerClosed = document.cookie
        .split('; ')
        .some(cookie => cookie.startsWith('modern_banner_closed='));

    if (!isBannerClosed) {
        // デバイス判定
        const isMobile = window.innerWidth <= 480;

        // スクロール監視の開始
        let scrollTimeout;
        window.addEventListener('scroll', handleScroll);

        // モバイルの場合は遅延表示
        if (isMobile) {
            setTimeout(() => {
                banner.classList.add('is-visible');
                window.removeEventListener('scroll', handleScroll);
            }, 2000);
        }

        // 画面サイズ変更時の処理
        let resizeTimeout;
        window.addEventListener('resize', () => {
            if (resizeTimeout) {
                clearTimeout(resizeTimeout);
            }
            resizeTimeout = setTimeout(() => {
                if (window.innerWidth <= 480) {
                    banner.classList.add('sp');
                    banner.classList.remove('pc');
                } else {
                    banner.classList.add('pc');
                    banner.classList.remove('sp');
                }
            }, 100);
        });

        // 初期表示時のデバイス判定
        if (isMobile) {
            banner.classList.add('sp');
        } else {
            banner.classList.add('pc');
        }
    } else {
        // バナーが非表示設定の場合は要素を削除
        banner.remove();
    }
});