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
        inputValue: '',
    },
    getList() {
        const that = this

        app.request('/share/cityList', {}, function(data, ret) {
            console.log(data)

            let cities = []
            const words = Object.keys(data)

            words.forEach((item, index) => {
                cities[index] = {
                    key: item,
                    list: data[item].map((n, i) => ({...n, key: n.id })),
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
    onLoad() {
        this.getList()
    },
    onChange(e) {
        console.log(e)
        const inputValue = e.detail.value
        this.setData({ inputValue })
        this.searchCity(inputValue)
    },
    onFocus(e) {
        this.setData({
            arrowInput: true,
        })
        console.log(e)
    },
    onBlur(e) {
        console.log(e)
    },
    onClear() {
        this.setData({
            inputValue: '',
            arrowInput: false,
            searchCityList: null,
        })
    },
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
    searchCity(cities_name) {
        const that = this

        if (that.timeout) {
            clearTimeout(that.timeout)
            that.timeout = null
        }

        that.timeout = setTimeout(function() {
            app.request('/share/searchCity', { cities_name }, function(data, ret) {
                console.log(data)
                that.setData({
                    searchCityList: data && data.searchCityList || null,
                })
            }, function(data, ret) {
                console.log(data)
                app.error(ret.msg)
            })
        }, 250)
    },
})