/**
 * バナー管理画面用JavaScript
 */
document.addEventListener('DOMContentLoaded', () => {
    // 画像アップロード処理
    const initializeImageUploader = () => {
        const uploadButtons = document.querySelectorAll('.image-upload-button');
        
        uploadButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                // 親要素を取得
                const uploadWrap = button.closest('.image-upload-wrap');
                const imageUrlInput = uploadWrap.querySelector('.image-url');
                const preview = uploadWrap.querySelector('.image-preview');
                
                // メディアアップローダーを作成
                const mediaUploader = wp.media({
                    title: '画像を選択',
                    library: {
                        type: 'image'
                    },
                    button: {
                        text: '画像を設定'
                    },
                    multiple: false
                });

                // 既存の画像があれば選択状態にする
                if (imageUrlInput.value) {
                    mediaUploader.on('open', () => {
                        const selection = mediaUploader.state().get('selection');
                        selection.reset([wp.media.attachment(imageUrlInput.value)]);
                    });
                }

                // 画像選択時の処理
                mediaUploader.on('select', () => {
                    const image = mediaUploader.state().get('selection').first().toJSON();
                    
                    // 画像URLを入力欄にセット
                    imageUrlInput.value = image.url;

                    // プレビュー画像の更新
                    if (!preview) {
                        const newPreview = document.createElement('img');
                        newPreview.className = 'image-preview';
                        newPreview.style.maxWidth = '200px';
                        newPreview.alt = '';
                        uploadWrap.appendChild(newPreview);
                        newPreview.src = image.url;
                    } else {
                        preview.src = image.url;
                    }
                });

                mediaUploader.open();
            });
        });
    };

    // カスタムCSSのプレビュー機能
    const initializeCustomCssPreview = () => {
        const cssTextarea = document.querySelector('textarea[name="custom_css"]');
        if (!cssTextarea) return;

        const previewStyle = document.createElement('style');
        document.head.appendChild(previewStyle);

        cssTextarea.addEventListener('input', (e) => {
            previewStyle.textContent = e.target.value;
        });
    };

    // フォームのバリデーション
    const initializeFormValidation = () => {
        const form = document.querySelector('form');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            const title = form.querySelector('input[name="title"]').value;
            const linkUrl = form.querySelector('input[name="link_url"]').value;
            const pcImage = form.querySelector('input[name="pc_image"]').value;
            
            if (!title || !linkUrl || !pcImage) {
                e.preventDefault();
                alert('タイトル、リンクURL、PC用バナー画像は必須項目です。');
            }
        });
    };

    // 初期化
    initializeImageUploader();
    initializeCustomCssPreview();
    initializeFormValidation();
});