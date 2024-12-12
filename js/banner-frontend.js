/**
 * フロントエンド用バナー制御スクリプト
 */
document.addEventListener('DOMContentLoaded', () => {
    const banners = document.querySelectorAll('.modern-banner');
    
    banners.forEach(banner => {
        // 閉じるボタンの処理
        const closeButton = banner.querySelector('.modern-banner-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                // バナーを非表示
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(100%)';
                
                // アニメーション完了後に要素を削除
                setTimeout(() => {
                    banner.remove();
                }, 300);

                // Cookieに非表示状態を保存（24時間）
                const bannerId = banner.dataset.id;
                const expires = new Date();
                expires.setTime(expires.getTime() + (24 * 60 * 60 * 1000));
                document.cookie = `modern_banner_${bannerId}=closed; expires=${expires.toUTCString()}; path=/`;
            });
        }

        // Cookieチェック
        const bannerId = banner.dataset.id;
        const isClosed = document.cookie
            .split('; ')
            .some(cookie => cookie.startsWith(`modern_banner_${bannerId}=closed`));

        if (isClosed) {
            banner.remove();
            return;
        }

        // スクロール位置による表示制御
        const handleScroll = () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            
            // 画面の20%以上スクロールしたら表示
            if (scrollTop > windowHeight * 0.2) {
                banner.classList.add('is-visible');
                // スクロールイベントを解除
                window.removeEventListener('scroll', handleScroll);
            }
        };

        // スマートフォンの場合は遅延表示
        if (window.innerWidth <= 480) {
            setTimeout(() => {
                banner.classList.add('is-visible');
            }, 2000);
        } else {
            // PCの場合はスクロールで表示
            window.addEventListener('scroll', handleScroll);
        }

        // デバイスサイズに応じたクラス付与
        const updateDeviceClass = () => {
            if (window.innerWidth <= 480) {
                banner.classList.add('sp');
                banner.classList.remove('pc');
            } else {
                banner.classList.add('pc');
                banner.classList.remove('sp');
            }
        };

        // 初期化時とリサイズ時にデバイスクラスを更新
        updateDeviceClass();
        window.addEventListener('resize', updateDeviceClass);
    });
});