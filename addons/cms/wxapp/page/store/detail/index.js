const app = getApp()
const data = {
    "store_info": {
        "id": 1,
        "city_id": 38,
        "store_name": "成都天润出行服务有限公司",
        "store_address": "成华区建材路39号隆鑫九熙广场二期1栋1515",
        "phone": "16352452148",
        "store_img": [
            "/uploads/20181121/169bb62e725078905de4cc84f4166307.jpg",
            "/uploads/20181121/de4ee95d8b0e610e14e772ea51f392bb.jpg"
        ],
        "longitude": "104.139225",
        "latitude": "30.644425"
    },
    "isLogic": [
        {
            "id": 1,
            "coupon_name": "满减优惠",
            "circulation": 100,
            "city_ids": "38",
            "user_id": {
                "1": "3",
                "2": "3",
                "3": "4",
                "4": "5",
                "5": "6"
            },
            "display_diagramimages": "/uploads/20181116/0894fd16abe33a256ac82236540d05ae.jpg",
            "coupon_amount": 2000,
            "threshold": "1000",
            "models_ids": "",
            "membership_grade": 0,
            "limit_collar": 3,
            "release_datetime": 1542535087,
            "validity_datetime": null,
            "createtime": 1542362296,
            "updatetime": 1542438057,
            "ismenu": 1,
            "use_id": null,
            "store_ids": ",1,2,",
            "remaining_amount": 99
        }
    ],
    "store_carList": [
        {
            "id": 1,
            "store_name": "成都天润出行服务有限公司",
            "planacar_count": [
                {
                    "id": 148,
                    "financial_platform_id": 6,
                    "models_id": 15,
                    "payment": 29800,
                    "monthly": 2895,
                    "nperlist": "36",
                    "margin": 0,
                    "tail_section": 0,
                    "gps": 1500,
                    "note": "",
                    "ismenu": 1,
                    "working_insurance": "yes",
                    "total_payment": null,
                    "sales_id": null,
                    "createtime": 1539935447,
                    "updatetime": 1542684599,
                    "category_id": 13,
                    "acar_status": 1,
                    "subject_id": 2,
                    "modelsimages": "/uploads/20181116/c6688df48871bffb28c0afc15c020ce4.jpg",
                    "recommendismenu": 1,
                    "flashviewismenu": 0,
                    "guide_price": 0,
                    "models_main_images": "/uploads/20181116/7a78ad19d7a7d79d15a8bf9dbac97bd2.png",
                    "label_id": "2",
                    "specialismenu": 1,
                    "specialimages": "/uploads/20181115/d4111a2a36fd1520957a8a1975601e37.jpg",
                    "store_id": 1,
                    "popularity": null,
                    "weigh": 158,
                    "subjectismenu": 1
                }
            ],
            "usedcar_count": [
                {
                    "id": 46,
                    "sales_id": null,
                    "licenseplatenumber": "川AN11Y1",
                    "models_id": 33,
                    "kilometres": 59598,
                    "companyaccount": "四轮",
                    "newpayment": 3000,
                    "monthlypaymen": 2480,
                    "periods": 26,
                    "totalprices": 61520,
                    "bond": 3000,
                    "tailmoney": 0,
                    "drivinglicenseimages": "/uploads/20181029/4f1616da72f256946c83ec7fc24cd032.jpg",
                    "vin": "LJDGAA2CXH0505109",
                    "engine_number": "H1048343",
                    "expirydate": "",
                    "annualverificationdate": "2019-10-18",
                    "carcolor": "白",
                    "aeratedcard": "有",
                    "volumekeys": 1,
                    "Parkingposition": "总部停车场",
                    "lending_date": null,
                    "car_images": null,
                    "bank_card": null,
                    "invoice_monney": null,
                    "registration_code": null,
                    "tax": null,
                    "business_risks": null,
                    "insurance": null,
                    "mortgage_type": null,
                    "shelfismenu": 1,
                    "vehiclestate": "可卖",
                    "note": "",
                    "createtime": 1540796956,
                    "updatetime": 1542784281,
                    "status_data": "",
                    "modelsimages": "/uploads/20181116/c6688df48871bffb28c0afc15c020ce4.jpg",
                    "guide_price": 0,
                    "models_main_images": "/uploads/20181116/7a78ad19d7a7d79d15a8bf9dbac97bd2.png",
                    "label_id": "2",
                    "store_id": 1,
                    "popularity": null,
                    "weigh": 46,
                    "car_licensedate": null
                }
            ],
            "logistics_count": [
                {
                    "id": 1,
                    "name": "瑞驰EC35",
                    "payment": 23800,
                    "monthly": 2483,
                    "nperlist": "36",
                    "margin": 3000,
                    "note": "三元锂电池，含保险   对个人",
                    "total_price": 83900,
                    "ismenu": 1,
                    "createtime": 1542347199,
                    "updatetime": 1542347199,
                    "subject_id": 0,
                    "modelsimages": "",
                    "recommendismenu": 0,
                    "flashviewismenu": 0,
                    "models_main_images": "",
                    "label_id": "",
                    "specialismenu": 0,
                    "specialimages": "",
                    "store_id": 1,
                    "popularity": null,
                    "subjectismenu": 0
                },
                {
                    "id": 2,
                    "name": "瑞驰EC35",
                    "payment": 26980,
                    "monthly": 2344,
                    "nperlist": "36",
                    "margin": 3000,
                    "note": "三元锂电池，含保险   对公司",
                    "total_price": 83900,
                    "ismenu": 1,
                    "createtime": 1542347347,
                    "updatetime": 1542347347,
                    "subject_id": 0,
                    "modelsimages": "",
                    "recommendismenu": 0,
                    "flashviewismenu": 0,
                    "models_main_images": "",
                    "label_id": "",
                    "specialismenu": 0,
                    "specialimages": "",
                    "store_id": 1,
                    "popularity": null,
                    "subjectismenu": 0
                }
            ]
        }
    ]
}

Page({
    data: {
        ...data,
    },
    onLoad(options) {
        console.log(options)
        this.options = options
        this.getDetail()
    },
    getDetail() {
        const store_id = this.options.id

      app.request('/store/store_details', { store_id }, (data, ret) => {
            console.log(data)
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
})