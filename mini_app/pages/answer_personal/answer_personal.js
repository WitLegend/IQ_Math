const qcloud = require('../../vendor/wafer2-client-sdk/index')
const config = require('../../config')
const util = require('../../utils/util.js')
const app = getApp()
const option = {
  CHOICE_DELAY_SHOW: 1500,//选项延时1.5S显示
}

Page({
  data: {
    userInfo_me: '', //本人用户信息
    countdown: 10,//倒计时
    question: '',
    hasClick: false,//判断是否已选答案，不能重新选择
    localClick: false,//是否本地单击的答案
    clickIndex: '',//判断用户选择了哪个答案
    answerColor: '',//根据选择正确与否给选项添加背景颜色
    scoreMyself: 0,//自己的总分
    sendNumber: 0,//每一轮的答题次数不能超过1次
    game_over: false,  //判断此次PK是否结束
    question_num: 5,    //题目剩余数量
    sure_num:0,
    finsh:false,
  },
  onLoad(options) {
    // console.log(app.appData.openId)
    var that = this
    that.setData({
      userInfo_me: app.appData.userInfo
    })
    // app.appData.fromClickId = options.currentClickId
    // app.upDateUser_networkFromClickId = require('../../utils/upDateUser_networkFromClickId.js').upDateUser_networkFromClickId
    wx.showShareMenu({
      withShareTicket: true
    })
    //获得一道题目
    wx.request({
      url: config.service.getquestionUrl,
      data: {},
      success(res) {
        that.setData({
          question: res.data.data,
          animate_showChoice: 'fadeIn',
          question_num: that.data.question_num - 1,
          countdown: 10,
        })
      }
    })

    this.time_out()
    clearTimeout(that.data.countdown)
    this.startAnimate()//定义开始动画
  },

  time_out() {
    // let countdown = that.data.countdown;
    const that = this
    var timer
    timer = setInterval(function () {
      // countdown--
      that.setData({
        countdown: that.data.countdown - 1
      })
      if (that.data.countdown == 0 && !that.data.finsh) {
        that.get_nextquestion()
        that.setData({
          countdown:10
        })
        // clearInterval(timer)
      }
    }, 1000)
  },

  get_nextquestion() {
    const that = this
    if (that.data.question_num < 0 && !that.data.localClick && !that.data.finsh){
      wx.request({
        url: config.service.setrecordUrl,
        data: {
          openid: app.appData.openId,
          score: that.data.scoreMyself,
          sure_num: that.data.sure_num
        },
        success(res) {
          that.setData({
            finsh:true
          })
          setTimeout(function () { //答完题显示战果
            that.setData({
              game_over: true,
              win: 1,
            })
          }, 2000)
        }
      })
    }else{
      wx.request({
        url: config.service.getquestionUrl,
        data: {},
        success(res) {
          if (that.data.localClick) {
            that.setData({
              question: res.data.data,
            })
          } else {
            that.setData({
              question: res.data.data,
              question_num: that.data.question_num - 1,
            })
          }
          that.reset()
        }
      })

      // var t = setTimeout(function () {
      //   wx.request({
      //     url: config.service.getquestionUrl,
      //     data: {},
      //     success(res) {
      //       if(that.data.localClick){
      //         that.setData({
      //           question: res.data.data,
      //         })
      //       }else{
      //         that.setData({
      //           question: res.data.data,
      //           question_num: that.data.question_num - 1,
      //         })
      //       }
      //       that.reset()
      //     }
      //   })
      // }, 10000)
      // th.time_out(that)
    }
  },
  
  reset() {
    var that = this
      that.setData({
      countdown: 10,//倒计时
      hasClick: false,//判断是否已选答案，不能重新选择
      localClick: false,//是否本地单击的答案
      clickIndex: '',//判断用户选择了哪个答案
      answerColor: '',//根据选择正确与否给选项添加背景颜色
      sendNumber: 0,//每一轮的答题次数不能超过1次
      animate_rightAnswer:''
    })
  },


  answer(e) {//开始答题
    const that = this
    if (!that.data.localClick) {  //防止重新选择答案
      that.setData({
        question_num: that.data.question_num - 1
      })
      if (e.currentTarget.dataset.right) {//判断答案是否正确
        that.setData({
          clickIndex: e.currentTarget.dataset.index,
          answerColor: 'right',
          sure_num:that.data.sure_num + 1,
        })
        //答对了则加分，时间越少加分越多,总分累加
        that.setData({
          scoreMyself: that.data.scoreMyself + that.data.countdown * 10
        })
      } else {
        that.setData({
          clickIndex: e.currentTarget.dataset.index,
          answerColor: 'error'
        })
      }
      that.setData({
        animate_rightAnswer: 'right',
        localClick: true//本地已经点击,若hasClick仍未false，则说明没有发送数据出去
      })
      if (that.data.question_num < 0 && !that.data.finsh)
      {
        console.log(1)
        wx.request({
          url: config.service.setrecordUrl,
          data:{
            openid: app.appData.openId,
            score:that.data.scoreMyself,
            sure_num:that.data.sure_num
          },
          success(res) {
            that.setData({
              finsh:true
            })
            setTimeout(function () { //答完题显示战果
              that.setData({
                game_over: true,
                win: 1,
              })
            }, 2000)
          }
        })
      }else{
        this.get_nextquestion(that)
      }
    }
  },

  //分享函数
  onShareAppMessage(res) {
    const that = this;
    return {
      title: '谁才是算数领域的天才？比比看吧！',
      path: `/pages/index/index?currentClickId=${app.appData.currentClickId}`,
      success(res) {
        //转发时向用户关系表中更新一条转发记录(个人为person，群为GId)。
        require('../../utils/upDateShareInfoToUser_network.js').upDateShareInfoToUser_network(app, that, res)
        wx.redirectTo({
          url: '../entry/entry'
        })
      }
    }
  },


  continue_fighting() {
    wx.reLaunch({
      url: '../index/index',
    })
  },
  
  startAnimate() {
    const that = this
    that.setData({
      zoomIn: 'zoomIn'
    })
    setTimeout(function () {
      that.setData({
        zoomOut: 'zoomOut'
      })
    }, 1500)
  }
})