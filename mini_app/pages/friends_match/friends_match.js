// pages/friends_math/friends_math.js
var qcloud = require('../../vendor/wafer2-client-sdk/index')
var config = require('../../config')
var util = require('../../utils/util.js')
const match = require('../../utils/tunnelMacth.js').match//引入匹配函数
const app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    status_name: '初始化...',
    room_id : '',
    other_is_join: false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (opt) {
    var that = this
    this.setData({
      userInfo:app.appData.userInfo,
      room_id : opt.room_id,
      status: opt.status
    })

    // app.appData.fromClickId = opt.currentClickId
    // app.upDateUser_networkFromClickId = require('../../utils/upDateUser_networkFromClickId.js').upDateUser_networkFromClickId
    wx.showShareMenu({
      withShareTicket: true
    })
    this.setData({ opt })
    if (opt.status == 2) {  //打开页面，若含opt.status == 2,则表示改页面来自转发
      let val = {
        room_id: opt.room_id,
        openid: app.appData.openId,
        type: 'join_room'
      }
      app.appData.wss = wx.connectSocket({
        url: config.service.socket,
      })
      app.appData.wss.onOpen(function (res) {
        app.appData.wss.send({
          data: JSON.stringify(val),
          success: function (res) {
            setTimeout( function () {
              wx.reLaunch({
                url: '../fighting_room/fighting_room?room_id=' + that.data.room_id + '&openid=' + app.appData.openId+'&status='+that.data.status,
              })
            },4000)
          }
        })
      })
      app.appData.wss.onMessage(function (res) {
        app.appData.wssData = JSON.parse(res.data).data
        console.log(JSON.parse(res.data).data)
      })
      let timer
      timer = setInterval(function () {
        if (app.appData.wssData.is_join == 1) {
          app.appData.other_info = app.appData.wssData.other_info
          that.setData({
            user_others: app.appData.wssData.other_info
          })
          clearInterval(timer)
        }
      }, 500)
      // setTimeout(function(){
      //   app.appData.other_info = app.appData.wssData.other_info
      //   that.setData({
      //     user_others: app.appData.wssData.other_info
      //   })
      // },300)
    } else {
      //  每0.5秒监听一次返回信息
      let timer
      timer = setInterval(function () {
        if (app.appData.wssData.is_join == 1) {
          //  发起者  至此得到  对战者  的信息
          app.appData.other_info = app.appData.wssData.other_info
          that.setData({
            user_others: app.appData.wssData.other_info
          })
          clearInterval(timer)
          // wx.reLaunch({
          //   url: '../fighting_room/fighting_room?room_id=' + that.data.room_id + '&openid=' + app.appData.openId,
          // })
          setTimeout(function () {
            wx.reLaunch({
              url: '../fighting_room/fighting_room?room_id=' + that.data.room_id + '&openid=' + app.appData.openId + '&status=' + that.data.status,
            })
          },4000)
        }
      }, 500)
    }
    // app.pageGetUserInfo(this, match(this, app, opt))//开始匹配
  },

  goback() {
    wx.reLaunch({
      url: '../index/index',
    })
  },

  onShareAppMessage(res) {
    const that = this
    return {
      title: '谁才是算数领域的天才？比比看吧！',
      path: '/pages/friends_match/friends_match?status=2&room_id=' + that.data.room_id,
    }
  },


  storeFriensNetwork() {
    const that = this;
    let [page, app] = [this, getApp()];
    let baseData = {
      openId: this.data.openId,
      appId: app.appData.appId,
      fromOpenId: this.data.opt.fromOpenId,
      fromGId: ''
    }
    wx.getShareInfo({
      shareTicket: app.appData.opt.shareTicket,  //当是从后台打开转发小程序，这时无法获取群信息
      success: (res) => {
        if (app.appData.gId) {
          baseData.fromGId = app.appData.gId
          storeFriendsNetwork(baseData)
        } else {
          app.gIdReadyCallback = (gId) => {
            baseData.fromGId = gId
            storeFriendsNetwork(baseData)
          }
        }
      },
      fail(res) {
        storeFriendsNetwork(baseData)
      }
    })
    function storeFriendsNetwork(data) {
      const that = this;
      qcloud.request({
        login: false,
        url: `${app.appData.baseUrl}storeFriendsNetwork`,
        data,
        success(res) {
          console.info('【storeFriensNetwork】：存储finalData和clickId成功')
        },
        fail(error) {
          util.showModel('请求失败', error);
          console.log('request fail', error);
        },
      });
    }
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
})