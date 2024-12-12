/**
 * バナー管理画面用のJavaScript
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

                    // 画像の更新時にプレビューを更新
                    updateImagePreview(uploadWrap);
                });

                mediaUploader.open();
            });
        });
    };

    // 画像設定のプレビュー更新
    const updateImagePreview = (wrap) => {
        const preview = wrap.querySelector('.image-preview');
        if (!preview) return;

        const loadingSelect = wrap.closest('td').querySelector('select[name*="loading"]');
        const fetchPrioritySelect = wrap.closest('td').querySelector('select[name*="fetchpriority"]');
        const altInput = wrap.closest('td').querySelector('input[name*="alt"]');
        const customAttrsTextarea = wrap.closest('td').querySelector('textarea[name*="custom_attributes"]');

        // 属性の更新
        if (loadingSelect) {
            preview.setAttribute('loading', loadingSelect.value);
        }
        if (fetchPrioritySelect) {
            preview.setAttribute('fetchpriority', fetchPrioritySelect.value);
        }
        if (altInput) {
            preview.setAttribute('alt', altInput.value);
        }

        // カスタム属性の適用
        if (customAttrsTextarea) {
            const customAttrs = parseCustomAttributes(customAttrsTextarea.value);
            Object.entries(customAttrs).forEach(([key, value]) => {
                preview.setAttribute(key, value);
            });
        }
    };

    // カスタム属性のパース
    const parseCustomAttributes = (attributesString) => {
        const attrs = {};
        const matches = attributesString.match(/([a-zA-Z0-9_-]+)=['"](.*?)['"]/g);
        
        if (matches) {
            matches.forEach(match => {
                const [key, value] = match.split('=');
                attrs[key] = value.replace(/['"]/g, '');
            });
        }
        return attrs;
    };

    // フォームの変更監視
    const initializeFormWatcher = () => {
        const imageSettings = document.querySelectorAll('.image-settings');
        
        imageSettings.forEach(settings => {
            const inputs = settings.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('change', () => {
                    updateImagePreview(settings.closest('td').querySelector('.image-upload-wrap'));
                });
            });
        });
    };

    // カスタムCSSのライブプレビュー
    const initializeCustomCssPreview = () => {
        const cssTextarea = document.querySelector('textarea[name="custom_css"]');
        const previewStyle = document.createElement('style');
        document.head.appendChild(previewStyle);

        if (cssTextarea) {
            cssTextarea.addEventListener('input', (e) => {
                previewStyle.textContent = e.target.value;
            });
        }
    };

    // フォームのバリデーション
    const initializeFormValidation = () => {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', (e) => {
                const linkUrl = form.querySelector('input[name="link_url"]').value;
                const pcImage = form.querySelector('input[name="pc_image"]').value;
                
                if (!linkUrl || !pcImage) {
                    e.preventDefault();
                    alert('リンクURLとPC用バナー画像は必須です。');
                }
            });
        }
    };

    // 初期化
    initializeImageUploader();
    initializeFormWatcher();
    initializeCustomCssPreview();
    initializeFormValidation();
});