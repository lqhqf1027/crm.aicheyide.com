const app = getApp()

Page({
    data: {
        cities: [],
        citys: [],
        tags: [{
            cities_name: '成都',
            id: 38,
        }, {
            cities_name: '南充',
            id: 39,
        }, {
            cities_name: '西安',
            id: 41,
        }, {
            cities_name: '昆明',
            id: 35,
        }, {
            cities_name: '银川',
            id: 40,
        }],
    },
    getList() {
        const that = this

        app.request('/index/cityList', {}, function(data, ret) {
            console.log(data)

            let cities = []
            const words = Object.keys(data.cityList)

            words.forEach((item, index) => {
                cities[index] = {
                    key: item,
                    list: data.cityList[item].map((n, i) => ({...n, key: n.id })),
                }
            })

            console.log(cities)

            that.setData({
                cities,
            })

        }, function(data, ret) {
            console.log(data)
            app.error(ret.msg)
        })
    },
    onChange(event) {
        console.log(event.detail, 'click right menu callback data')
    },
    onLoad() {
        this.getList()
    },
    onReady() {},
    onClick(e) {
        const { value } = e.currentTarget.dataset
        console.log(value)

        if (!value.citys.length) return

        this.setData({
            citys: value.citys,
            visible: true,
        })
    },
    onClose() {
        this.setData({
            visible: false,
        })
    },
    onSelect(e) {
        const { value } = e.currentTarget.dataset
        const city = {
            cities_name: value.cities_name,
            id: value.id,
        }

        wx.setStorage({
            key: 'city',
            data: city,
            success() {
                wx.navigateBack()
            },
        })

        console.log(value)
    },
    onCancel() {
        wx.navigateBack()
    },
})