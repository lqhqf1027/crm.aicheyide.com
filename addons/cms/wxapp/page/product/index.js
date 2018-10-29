const { Tab } = require('../../assets/libs/zanui/index');

var app = getApp();
Page(Object.assign({}, Tab, {
  data: {
    bannerList: [],
    archivesList: [],
    loading: false,
    nodata: false,
    nomore: false,
    tab: {
      list: [],
      selectedId: '0',
      scroll: true,
      height: 44
    },
    

    value1: [],
    value2: [],
    value3: [],
    value4: [],

  },
  //模型的ID,这里使用产品的模型
  model: 2,
  channel: 0,
  page: 1,
  onLoad: function (option) {
    console.log(option);
    var that = this;
    this.channel = 0;
    this.page = 1;
    this.setData({ ["tab.list"]: app.globalData.productTabList });
    this.loadArchives();
  },
  onPullDownRefresh: function () {
    this.setData({ nodata: false, nomore: false });
    this.page = 1;
    this.loadArchives(function () {
      wx.stopPullDownRefresh();
    });
  },
  onReachBottom: function () {
    var that = this;
    this.loadArchives(function (data) {
      if (data.archivesList.length == 0) {
        app.info("暂无更多数据");
      }
    });
  },
  loadArchives: function (cb) {
    var that = this;
    if (that.data.nomore == true || that.data.loading == true) {
      return;
    }
    this.setData({ loading: true });
    app.request('/archives/index', { model:this.model, channel: this.channel, page: this.page }, function (data, ret) {
      that.setData({
        loading: false,
        nodata: that.page == 1 && data.archivesList.length == 0 ? true : false,
        nomore: that.page > 1 && data.archivesList.length == 0 ? true : false,
        archivesList: that.page > 1 ? that.data.archivesList.concat(data.archivesList) : data.archivesList,
      });
      that.page++;
      typeof cb == 'function' && cb(data);
    }, function (data, ret) {
      app.error(ret.msg);
    });
  },
  handleZanTabChange(e) {
    var componentId = e.componentId;
    var selectedId = e.selectedId;
    this.channel = selectedId;
    this.page = 1;
    this.setData({
      nodata: false,
      nomore: false,
      [`${componentId}.selectedId`]: selectedId
    });
    wx.pageScrollTo({ scrollTop: 0 });
    this.loadArchives();
  },


  openCalendar1() {
    $wuxCalendar().open({
      value: this.data.value1,
      onChange: (values, displayValues) => {
        console.log('onChange', values, displayValues)
        this.setData({
          value1: displayValues,
        })
      },
    })
  },
  openCalendar2() {
    $wuxCalendar().open({
      value: this.data.value2,
      multiple: true,
      onChange: (values, displayValues) => {
        console.log('onChange', values, displayValues)
        this.setData({
          value2: displayValues,
        })
      },
    })
  },
  openCalendar3() {
    $wuxCalendar().open({
      value: this.data.value3,
      direction: 'vertical',
      onChange: (values, displayValues) => {
        console.log('onChange', values, displayValues)
        this.setData({
          value3: displayValues,
        })
      },
    })
  },
  openCalendar4() {
    const now = new Date()
    const minDate = now.getTime()
    const maxDate = now.setDate(now.getDate() + 7)

    $wuxCalendar().open({
      value: this.data.value4,
      minDate,
      maxDate,
      onChange: (values, displayValues) => {
        console.log('onChange', values, displayValues)
        this.setData({
          value4: displayValues,
        })
      },
    })
  },
  requests:function (){
    wx.request({
      url:'https://crm.aicheyide.com/admin/planmanagement/plantabs/getInfo',
      success:function (res){
          console.log(res);
      },
      complete:function (){
        console.log(11);
      }
    });

    // wx.downloadFile({
    //   url: 'C:/Users/EDZ/Desktop/images/img1', //仅为示例，并非真实的资源
    //   success(res) {
    //     // 只要服务器有响应数据，就会把响应内容写入文件并进入 success 回调，业务需要自行判断是否下载到了想要的内容
    //     console.log(res);
    //   },
    //   fail:function (res){
    //     console.log(res);
    //   }
    // })

  },

 
  

}))