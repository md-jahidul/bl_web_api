<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        span {
            font-family: 'Inter', sans-serif;
        }

        .heading {
            color: #212121;
            text-align: right;
            font-size: 24px;
            font-style: normal;
            font-weight: 700;
            line-height: 120%;
        }

        .box-1 {

            width: 100%;
            padding: 12px;
            margin-top: 40px;
            border-radius: 8px;
            border: 1px solid #FBD1BD;
            border-collapse: separate;
            background: linear-gradient(90deg, #FFF 0%, #FFFAF8 100%);

        }

        .box-2 {
            width: 100%;
            border-radius: 8px;
            border: 1px solid #E0E0E0;
            padding: 12px;
        }

        .box-3 {
            width: 100%;
            border-radius: 8px;
            border: 1px solid #E0E0E0;
            padding: 12px;
        }

        .box-3 .item {
            border-bottom: 1px solid #E0E0E0;
            display: block;
            min-height: 40px;
        }

        .box-3 .item {
            margin-bottom: 10px;
        }

        .box-3 .item:last-child {
            border-bottom: none;
            margin-bottom: 0px;
        }

        .box-3 .left {
            float: left;
        }

        .box-3 .right {
            float: left;
            padding-left: 10px;
            margin-top: -4px;
        }

        .txt-1 {
            display: block;
            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            color: #757575;
            margin-bottom: 5px;
            letter-spacing: 0.2px;
            font-family: 'Inter', sans-serif;

        }

        .txt-2 {
            font-size: 12px;
            font-style: normal;
            font-weight: 600;
            line-height: 130%;
            font-family: 'Inter', sans-serif;

        }

        .txt-3 {
            color: #212121;
            font-family: 'Inter', sans-serif;

            font-size: 14px;
            font-style: normal;
            font-weight: 700;
            line-height: 130%;
            letter-spacing: 0.28px;
        }

        .txt-4 {
            color: #616161;
            display: block;
            font-family: 'Inter', sans-serif;


            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            line-height: 125%;
            letter-spacing: 0.2px;
        }

        .txt-5 {

            color: #757575;
            margin-top: 8px;
            font-family: 'Inter', sans-serif;

            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            line-height: 125%;
            letter-spacing: 0.2px;

        }

        .txt-6 {
            color: #757575;

            font-size: 10px;
            font-style: normal;
            font-weight: 400;
            line-height: 125%;
            display: block;
            margin-bottom: 6px;
            letter-spacing: 0.2px;
        }

        .txt-7 {
            color: #212121;

            font-size: 12px;
            font-style: normal;
            font-weight: 400;
            line-height: 125%;
            letter-spacing: 0.24px;
        }

        .startup-info .item{
            margin-bottom: 12px;
        }
        .personal-info tr:last-child .txt-2{
            margin-bottom: 0px;
        }
        .personal-info .txt-2{
            margin-bottom: 16px;
        }
    </style>
</head>

<body>
    @if($data['application_status'] == "draft")
        <div><img src="data:image/png;base64,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"/></div>
        <div style="margin-top: 20px">
            <h3><span style="color: #f16522">Warning:</span> Due to an incomplete final submission, your application is not yet available for download.</h3>
        </div>
    @else
        <table style="width: 100%;">
            <tr>
                <td><img src="data:image/png;base64,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"/></td>
                <td style="float:right;">
                    <h1 class="heading">Submitted Idea</h1>
                    <div style="float:right; width: 250px;">
                        @if(!empty($data['submitted_date']))
                            <span style="float: left;border-right: 1px solid #E0E0E0; text-align: right; padding-right: 20px;">
                    <span style="
                      display: block;
                      font-size: 10px;
                      font-style: normal;
                      font-weight: 400;
                      color: #757575;
                      line-height: 125%; /* 12.5px */
                      letter-spacing: 0.2px;">Submitted on</span>
                    <span style="
                      font-size: 12px;
                      font-weight: 600;
                      line-height: 130%;">{{ $data['submitted_date'] }}</span>
                </span>
                        @else
                            <span style="float: left;border-right: 1px solid #E0E0E0; text-align: right; padding-right: 20px;">
                    <span style="
                      display: block;
                      font-size: 10px;
                      font-style: normal;
                      font-weight: 400;
                      color: #757575;
                      line-height: 125%; /* 12.5px */
                      letter-spacing: 0.2px;">Application Status:</span>
                    <span style="
                      font-size: 12px;
                      font-weight: 600;
                      line-height: 130%;">Draft</span>
                </span>
                        @endif
                        <span style="float:right; padding-left: 16px; text-align: right;">
                <span style="
                  display: block;
                  font-size: 10px;
                  font-style: normal;
                  font-weight: 400;
                  color: #757575;
                  line-height: 125%; /* 12.5px */
                  letter-spacing: 0.2px;">ID Number</span>
                <span style="
                  font-size: 12px;
                  color:#F16522;
                  font-style: normal;
                  font-weight: 600;
                  line-height: 130%;">IDN. #{{ $data['application_id'] }}</span>
            </span>
                    </div>
                </td>
            </tr>
        </table>

        <table class="box-1">
            <tr>
                <td>
                    <div class="txt-1">Idea Name</div>
                    <div class="txt-2">{{ $data['summary']['idea_title'] }}</div>
                </td>
                <td>
                    <div class="txt-1">Industry</div>
                    <div class="txt-2">{{ $data['summary']['industry'] }}</div>
                </td>
            </tr>
        </table>
        <h4 style="heading-1">Personal Information</h4>
        <table class="box-2 personal-info">
            <tr>
                <td>
                    <div class="txt-1">Name</div>
                    <div class="txt-2">{{ $data['personal']['name'] }}</div>
                </td>
                <td>
                    <div class="txt-1">Gender</div>
                    <div class="txt-2">{{ $data['personal']['gender'] }}</div>
                </td>
                <td>
                    <div class="txt-1">Designation</div>
                    <div class="txt-2">Developer</div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="txt-1">Email</div>
                    <div class="txt-2">{{ $data['personal']['email'] }}</div>
                </td>
                <td>
                    <div class="txt-1">Contact Number</div>
                    <div class="txt-2">{{ $data['personal']['phone_number'] }}</div>
                </td>
            </tr>


            <tr>
                <td>
                    <div class="txt-1">Profession</div>
                    <div class="txt-2">{{ $data['personal']['profession'] }}</div>
                </td>
                <td>
                    <div class="txt-1">Institute/Organization</div>
                    <div class="txt-2">{{ $data['personal']['institute_or_org'] }}</div>
                </td>
                <td>
                    <div class="txt-1">Education</div>
                    <div class="txt-2">{{ $data['personal']['education'] }}</div>
                </td>
            </tr>
        </table>
        <h4 class="heading-1">
            Team Members</h4>
        <table class="box-2">
            <tr>
                @foreach($data['personal']['team_members'] as $member)
                    <td>
                        <div class="txt-3">{{ $member['name'] }}</div>
                        <div class="txt-4">{{ $member['designation'] }}
                        </div><div class="txt-5">{{ $member['email'] }}</div>
                    </td>
                @endforeach
            </tr>
        </table>
        <h4 class="heading-1">
            Startup Information</h4>
        <div class="box-2 startup-info">
            <div class="item">
                <div class="txt-6">
                    Problem Identification
                </div>
                <div class="txt-7">
                    {{ $data['startup']['problem_identification'] }}
                </div>
            </div>
            <div class="item">
                <div class="txt-6">
                    The Big Idea
                </div>
                <div class="txt-7">
                    {{ $data['startup']['big_idea'] }}
                </div>
            </div>
            <div class="item">
                <div class="txt-6">
                    Target Group
                </div>
                <div class="txt-7">
                    {{ $data['startup']['target_group'] }}
                </div>
            </div>
            <div class="item">
                <div class="txt-6">
                    Market Size
                </div>
                <div class="txt-7">
                    {{ $data['startup']['market_size'] }}
                </div>
            </div>
            <div class="item">
                <div class="txt-6">
                    Business Model
                </div>
                <div class="txt-7">
                    {{ $data['startup']['business_model'] }}
                </div>
            </div>
            <div class="item">
                <div class="txt-6">
                    GTM Plan
                </div>
                <div class="txt-7">
                    {{ $data['startup']['gtm_plan'] }}
                </div>
            </div>
            <div class="item">
                <div class="txt-6">
                    Financial Metrics
                </div>
                <div class="txt-7">
                    {{ $data['startup']['financial_metrics'] }}
                </div>
            </div>


            <div class="item">
                <div class="txt-6">
                    Is there any existing product or service for this problem?*
                </div>
                <div class="txt-7">
                    {{ $data['startup']['exist_product_service'] ? "Yes" : "No" }}
                </div>
            </div>

            {{-- If Yes  Start--}}
            @if($data['startup']['exist_product_service'])
                <div class="item">
                    <div class="txt-6">
                        Short Description
                    </div>
                    <div class="txt-7">
                        {{ $data['startup']['exist_product_service_details'] }}
                    </div>
                </div>
                <div class="item">
                    <div class="txt-6">
                        How is your product/service different from the existing one?*
                    </div>
                    <div class="txt-7">
                        {{ $data['startup']['exist_product_service_diff'] }}
                    </div>
                </div>
            @endif
            {{-- If Yes  End--}}

            <div class="item">
                <div class="txt-6">
                    Have you received any funding?*
                </div>
                <div class="txt-7">
                    {{ $data['startup']['receive_fund'] ? "Yes" : "No" }}
                </div>
            </div>
            {{-- If Yes received funding Start--}}
            @if($data['startup']['receive_fund'])
                <div class="item">
                    <div class="txt-6">
                        What is the source of your funding?
                    </div>
                    <div class="txt-7">
                        {{ $data['startup']['receive_fund_source'] }}
                    </div>
                </div>
            @endif
            {{-- If Yes received funding End--}}

            <div class="item">
                <div class="txt-6">
                    Which stage is your startup currently in?
                </div>
                <div class="txt-7">
                    {{ $data['startup']['startup_current_stage'] }}
                </div>
            </div>
        </div>

        <h4 class="heading-1">
            Attachments</h4>
        <div class="box-3">
            @foreach($data['attachments'] as $attachment)
                <div class="item">
                    <div class="left">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAASJJREFUSEvNluFxgzAMhSUmaTahm9T2IEkGsd1MEjYpk/j1XhrnOALYkHBX/nGW9Ul+8gOVnR/dOb88AWKMLYCjiLQL8A7AxTn3XSrwCRBC+BGRj9JGrgM4O+dOS7FTANQmV9VjCbIZYK1V7/2pBHkJwE5LkCoAAENB7wMQqZGqHowxfQlSBIzP+A65zujUW2sPw7UigGfNDZyuvDmEMDsIOT5D1gAwgO0PUNXPlBK1oCaP520dzHXzvwEcTY4ogOvbOsjzPvKo2zjGGAmjd23XYHzJAHRN01yMMZ33/utlkUWk58TkW5tLnaqea6tFvickxBDG95RSS5ObsvWtgBoHv8XUAKo/OBPUshcNHbO67L/ATlXPFH/R7FYmLYbv/lfxCycXCijGejvoAAAAAElFTkSuQmCC" />
                    </div>
                    <div class="right">
                        <span class="txt-2">{{ $attachment['file_name'] }}</span>
                        <span class="txt-1">File Size:<span style="font-weight: 600; color: #212121; margin-left:5px">{{ $attachment['file_size'] }}</span></span>
                    </div>
                </div>
            @endforeach
        </div>

        <table style="width: 100%; margin-top: 24px;">
            <tr>
                <td>
                    <span style="float: left; margin-right: 8px;"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAYAAABWdVznAAAAAXNSR0IArs4c6QAAAM5JREFUKFOVkTGrQQEUx3/n3XiDwbeQicn6SlFmNncwMOjmfQAfQDEjiYHC5iqLWVmUyWLwOQzqukdX6d6k574z/3+d3/8cwTYnQIVQo2PBNl1AQuXB9QD9GFb2CEkgHgZoU5w3sc0t8PM3oNqjtPhlWe4i0vBMnsAJ6AAjIPJQVBlQmlmszD6K9dT2gdhXhsstB2KDTjkmaqTOfaAe7OgriR4gmkevaRxnh/E9RLT6epDXDicMN4tjtN6Fgx0+XjbY4T+PU09pAxRCrljfAXAnR7fLbCGlAAAAAElFTkSuQmCC" /></span>
                    <span style="float: left;">
                <h5 style="margin:0; color: #212121;

                     font-size: 12px;
                     font-style: normal;
                     font-weight: 600;
                     line-height: 130%;">I consent to BL Labs sharing my information</h5>
                <p style="color: #616161;

                     font-size: 10px;
                     font-style: normal;
                     font-weight: 400;
                     line-height: 125%;
                     letter-spacing: 0.2px;">
                    I consent to BL Labs sharing the demographic information I provided above
                    and my contact
                    information to third-party partners who are seeking to help
                </p>
            </span>
                </td>
            </tr>
        </table>
        <span style="color:#212121;

         font-size: 12px;
         text-align: center;
         width: 100%;
         display: block;
         font-style: normal;
         font-weight: 400;
         line-height: 130%;">Questions or faq? Contact us at <span style="color:#F16522;">faq@banglalink.com.</span></span>
        <p style="color: #212121;
         text-align: center;

         font-size: 10px;
         font-style: normal;
         font-weight: 400;
         letter-spacing: 0.2px;">
            info@banglalink.net | +8801911304121 | For any query: Banglalink Digital
            Communications Limited
            | info@banglalink.netpr@banglalink.net | Tigers' Den, House 4 (SW), Bir Uttam Mir Shawkat Sharak Gulshan 1,
            Dhaka 1212, Bangladesh
        </p>
    @endif
</body>
</html>
