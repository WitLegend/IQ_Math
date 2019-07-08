// pages/index/index.js
var qcloud = require('../../vendor/wafer2-client-sdk/index')
var util = require('../../utils/util.js')
var config = require('../../config')
const app = getApp();


Page({

  /**
   * 页面的初始数据
   */
  data: {
    score:0,
    userInfo: {},
    logged: false,
    takeSession: false,
    requestResult: ''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this

    // if(this.data.userInfo){
    //   console.log(99)
    // }
    // if (app.appData.userInfo){
    // this.setData({
    //   userInfo: app.appData.userInfo
    //   })
    //   console.log(333)
    // }
    // console.log(app.appData.userInfo)

    if (app.appData.openId == ''){
      wx.login({
        success(res) {
          if (res.code) {
            // 获得openid
            wx.request({
              url: 'https://api.weixin.qq.com/sns/jscode2session?appid=' + config.service.appId + '&secret=' + config.service.secret + '&js_code=' + res.code + '&grant_type=authorization_code',
              data: {},
              success(res) {
                app.appData.openId = res.data.openid
                //获得用户的信息，从数据库
                wx.request({
                  url: config.service.getuserinfoUrl,
                  data: {
                    openid: app.appData.openId
                  },
                  success(res) {
                    if (JSON.stringify(res.data.data) !== '[]') {
                      app.appData.userInfo = res.data.data
                      that.setData({
                        userInfo: res.data.data
                      })
                      that.getscore()
                    } else {
                      //数据库中没有用户的信息，让用户授权
                      wx.reLaunch({
                        url: "/pages/login/login",
                      })
                    }
                  }
                })
              },
            })
          } else {
            console.log('登录失败！' + res.errMsg)
          }
        }
      })
    }else{
      this.getscore()
    }
  },

  getscore(){
    var that = this
    wx.request({
      url: config.service.getscoreUrl,
      data:{
        openid: app.appData.openId
      },
      success(res){
        that.setData({
          score: res.data.data ? res.data.data : 0
        })
      }
    })
  },

  onGetUserInfo: function (e) {
    if (!this.logged && e.detail.userInfo) {
      this.setData({
        logged: true,
        avatarUrl: e.detail.userInfo.avatarUrl,
        userInfo: e.detail.userInfo
      })
    }
  },

  gotoFriends() {
    wx.navigateTo({
      url: '../friends_sort/friends_sort'
    })
  },

  gotoFighting() {
    wx.navigateTo({
      url: '../answer_personal/answer_personal'
    })
  },

  gotoRank() {
    wx.navigateTo({
      url: '../rank/rank'
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})