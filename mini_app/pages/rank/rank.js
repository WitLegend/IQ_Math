var qcloud = require('../../vendor/wafer2-client-sdk/index')
var config = require('../../config')
var util = require('../../utils/util.js')
const app = getApp();
Page({
  data: {
    currentTab: 0,
    friendsData: [],
    globalData: [],
    loadNumber: 0,//全球排名数据加载次数
    avgNumber:0
  },
  onLoad: function (opt) {
    wx.showShareMenu({
      withShareTicket: true
    })
    this.setData({
      userInfo: app.appData.userInfo,
      openId:app.appData.openId,
    })
    // app.pageGetUserInfo(this)
    this.getRankGlobalData();
  },
  onShow() {
    this.getRankFriendsData();
  },
  onReachBottom: function () {//下拉加载
    const that = this
    if (that.data.currentTab) {
      that.getRankGlobalData()
    }
  },
  getRankGlobalData() {//加载全球排名的数据
    const that = this
    wx.request({
      url: config.service.getRankGlobalUrl,
      data: {
        loadNumber: that.data.loadNumber
      },
      success: (res) => {
        that.setData({
          globalData: that.data.globalData.concat(res.data.data),//数据叠加
          loadNumber: that.data.loadNumber + 1
        })
      },
      fail(error) {
        util.showModel('请求失败', error);
        console.log('request fail', error);
      },
    })
  },
  getRankFriendsData: function () {
    const that = this
    wx.request({
      url: config.service.getsavgrateUrl,
      data: {
        avgNumber: that.data.avgNumber
      },
      success: (res) => {
        that.setData({
          friendsData: that.data.friendsData.concat(res.data.data),//数据叠加
          avgNumber: that.data.avgNumber + 1
        })
      },
      fail(error) {
        util.showModel('请求失败', error);
        console.log('request fail', error);
      },
    })
  },
  swichNav(e) {
    var that = this;
    that.setData({
      currentTab: e.target.dataset.current,
    })
  },
})