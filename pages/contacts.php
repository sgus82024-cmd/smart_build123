<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контактная информация</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Itim&family=M+PLUS+Rounded+1c&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&family=Russo+One&family=Shafarik&display=swap');

.roboto-tree {
    font-family: "Roboto", sans-serif;
    font-optical-sizing: auto;
    font-weight: light;
    font-style: normal;
    font-variation-settings:
      "wdth" 100;
  }
  .inter-ttt {
    font-family: "Inter", sans-serif;
    font-optical-sizing: auto;
    font-weight: bold;
    font-style: normal;
  }
        body {
            font-family: 'Inter', sans-serif;
            background-color: rgba(0, 0, 0, 0.5);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        
        .modal-pattern {
               background-image: url(https://media.licdn.com/dms/image/v2/C511BAQHwyhgChF32-w/company-background_10000/company-background_10000/0/1583854866040/amplify_social_media_agency_cover?e=2147483647&v=beta&t=BeoB3BUheC_ljJf9UwIHh7-_1fmxBNae1SnMnKJY7tc);
    border-radius: 12px;
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
            width: 100%;
            max-width: 1000px;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            color: white;
            position: relative;
            
        }
        
        .modal-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 0;
        }
        
  button.close-btn {
    position: relative;
    text-align: center;
    color: #ff5b4d;
    background: transparent;
    border: none;
    width: 78px;
    height: 78px;
    display: flex
;
    font-size: 78px;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
        
        .close-btn-inner {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .section {
               padding: 0 32px 32px 32px;
    position: relative;
    display: flex
;
    flex-direction: column;
    flex-wrap: nowrap;
        }
        .contact-info a {
    font-family: 'Inter';
    font-style: normal;
    font-weight: 700;
    font-size: 12px;
    text-decoration: none;
    color: #7A1E4C;
    display: flex
;
    flex-direction: column;
    justify-content: center;
    align-content: flex-end;

}
.contact-info a:hover{
    text-decoration:underline;
}

        .section-title {

    font-family: 'Roboto';
    font-style: normal;
    font-weight: 300;
    font-size: 28px;
    line-height: 41px;
    display: flex
;
    align-items: center;
    text-align: center;
    color: #F9FAFB;
    justify-content: center;


}
        
        
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 24px;
            max-width: 448px;
            margin: 0 auto;
        }
        
        .contact-item {
          display: flex
;
    align-items: center;
    gap: 10px;
        text-align: center;
        }
        
        .contact-icon {
              width: 50px;
    height: 50px;
    display: flex
;
    align-items: center;
    justify-content: center;
    color: #7A1E4C;
    margin-right: 16px;
    font-size: 24px;
        }
        
        .contact-link {
            color: #7A1E4C;
            text-decoration: none;
        }
        
        .contact-link:hover {
            text-decoration: underline;
        }
        
        .address-text {
                margin: 0;
            color: #7A1E4C;
        }
        
        .address-subtext {
                margin: 0;
            color: #7A1E4C;
        }
        
        .map-container {
            background-image: url('https://public.readdy.ai/gen_page/map_placeholder_1280x720.png');
            background-position: center;
            background-size: cover;
            height: 256px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
                width: 95%;
    margin: 0 auto;
}
        
        
        .map-marker {
            position: absolute;
            bottom: 16px;
            right: 16px;
            background-color: white;
            color: black;
            padding: 8px 12px;
            border-radius: 9999px;
            font-size: 12px;
            display: flex;
            align-items: center;
        }
        
   
        
        .contact-form {
            max-width: 448px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-label {
          height: 25px;
    font-family: 'Roboto';
    font-style: normal;
    font-weight: 400;
    font-size: 16px;
    line-height: 19px;
    color: #FFFFFF;
    transform: scaleX(180deg);
    display: block;
        }
        
        .form-input, .form-textarea {
                box-sizing: border-box;
    width: 260px;
    height: 55px;
    color: white;
    background: #222529;
    border: 1px solid #888B93;
    border-radius: 4px;
    padding-left: 10px;
    transform: scaleX(180deg);
    padding: 10px;
        }
        
        .form-input:focus, .form-textarea:focus {
            border-color:rgb(255, 255, 255);
            outline: none;
        }
        
        .form-textarea {
            resize: none;
            height: 96px;
        }
        
        .submit-btn {
              background: #7A1E4C;
    border: 1px solid #7A1E4C;
    border-radius: 4px;
    color: white;
    border: 1px solid transparent;
    padding: 15px 40px;
    font-size: 12px;
    text-transform: uppercase;
    margin: 30px 0;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-block;
    font-family: "Inter", sans-serif;
    font-weight: 700;
    text-align: center;
    transform: scaleX(180deg);
        }
        
        .submit-btn:hover {
            color: var(--primary-color);
    border-color: var(--primary-color);
    transform: translateY(-2px);
    background: transparent;
        }
        
        .btn-container {
            display: flex;
            justify-content: center;
        }

        .back {
    width: 100%;
    display: flex
;
    justify-content: flex-end;
}
        
        @media (max-width: 640px) {
            .section {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="modal-pattern">
        <!-- Close button -->
         <div class="back" >
        <button class="close-btn" onclick="window.history.go(-1)">
            ×
        </button></div>
        
        <!-- Contact info section -->
        <div class="section">
            <h2 class="section-title">НАШИ КОНТАКТЫ</h2>
            
            <div class="contact-info">
                <!-- Phone -->
                <div class="contact-item">
                    <div class="contact-icon"><svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="50" height="50" fill="url(#pattern0_234_294)"/>
<defs>
<pattern id="pattern0_234_294" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_234_294" transform="scale(0.01)"/>
</pattern>
<image id="image0_234_294" width="100" height="100" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>
</div>
                    <a href="tel:8(937)-088-71-21" class="contact-link">8(937)-088-71-21</a>
                </div>
                
                <!-- Address -->
                <div class="contact-item">
                    <div class="contact-icon"><svg width="58" height="50" viewBox="0 0 58 50" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<g filter="url(#filter0_d_234_301)">
<rect x="4" width="50" height="50" fill="url(#pattern0_234_301)" shape-rendering="crispEdges"/>
</g>
<defs>
<filter id="filter0_d_234_301" x="0" y="0" width="58" height="58" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
<feFlood flood-opacity="0" result="BackgroundImageFix"/>
<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
<feOffset dy="4"/>
<feGaussianBlur stdDeviation="2"/>
<feComposite in2="hardAlpha" operator="out"/>
<feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
<feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_234_301"/>
<feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_234_301" result="shape"/>
</filter>
<pattern id="pattern0_234_301" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_234_301" transform="scale(0.01)"/>
</pattern>
<image id="image0_234_301" width="100" height="100" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>
</div>
                    <div>
                        <a href="https://yandex.ru/maps/-/CHveyO62" target="_blank">
                        <p class="address-text">НИКОЛЬСКАЯ УЛИЦА, 4/5</p>
                        <p class="address-subtext">МОСКВА</p></a>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="contact-item">
                    <div class="contact-icon"><svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="50" height="50" fill="url(#pattern0_234_305)"/>
<defs>
<pattern id="pattern0_234_305" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_234_305" transform="scale(0.01)"/>
</pattern>
<image id="image0_234_305" width="100" height="100" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>
</div>
                    <a href="mailto:smartbuild@gmail.com" class="contact-link">SMARTBUILD@GMAIL.COM</a>
                </div>
            </div>
        </div>
        
        <!-- Map section -->
        <div class="section" style="padding-top: 0;">
            <h2 class="section-title">МЫ НА КАРТЕ</h2>
            <div class="map-container">
               <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A5bfacb10eb8839b8d28198640c48e6e05467248e80a0b74b7f7692a17f94ace1&amp;source=constructor" width="100%" height="256" frameborder="0"></iframe></div>
                    
                </div>
                   <!-- Contact form -->
        <div class="section" style="padding-top: 0;">
            <h2 class="section-title">ОБРАТНАЯ СВЯЗЬ</h2>
            
            <form class="contact-form">
                <div class="form-group">
                    <label class="form-label">Введите Ф.И.О</label>
                    <input type="text" placeholder="Имя Фамилия" class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Введите E-mail</label>
                    <input type="email" placeholder="E-mail" class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Введите ваше обращение</label>
                    <textarea class="form-textarea" rows="4"></textarea>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="submit-btn">
                        ОТПРАВИТЬ
                    </button>
                </div>
            </form>
        </div>
    </div>
            </div>
            
        </div>
        
      
</body>
</html>