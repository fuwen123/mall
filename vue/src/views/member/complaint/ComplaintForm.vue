<template>
	<div class="container">
		<!-- header -->
		<div class="common-header-wrap">
			<mt-header title="投诉详情" class="common-header">
				<mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
			</mt-header>
		</div>
<div v-if="!complain_id">
		<!-- body -->
		<div class="body">
			<div @click="subjectVisible=true">
				<mt-cell title="投诉主题" is-link :value="subject_name" />
			</div>
			<mt-cell class="menu-item" title="投诉凭证" ></mt-cell>
			<div class="image-wrapper">
				<div class="user-avatar iconfont icon-xiangji" ref="upload_btn1" @change="uploadComplaintPic(0,$event)">
					<img v-if="image[0]" :src="image[0]">
					<input type="file" accept="image/jpg,image/png,image/gif,image/bmp,image/jpeg">
				</div>
				<div class="user-avatar iconfont icon-xiangji" ref="upload_btn2" @change="uploadComplaintPic(1,$event)">
					<img v-if="image[1]" :src="image[1]">
					<input type="file" accept="image/jpg,image/png,image/gif,image/bmp,image/jpeg">
				</div>
				<div class="user-avatar iconfont icon-xiangji" ref="upload_btn3" @change="uploadComplaintPic(2,$event)">
					<img v-if="image[2]" :src="image[2]">
					<input type="file" accept="image/jpg,image/png,image/gif,image/bmp,image/jpeg">
				</div>
			</div>
			<div class="order-comment-body">

				<div class="enter">
					<textarea
						placeholder="投诉内容"
						v-model="content"
					></textarea>
				</div>
			</div>
		</div>
		<mt-button type="primary" @click='addComplaint' class="ds-button-large">提交</mt-button>
		<!--投诉主题-->
		<mt-popup v-model="subjectVisible" position="right" class="common-popup-wrapper">
			<div class="common-header-wrap">
				<mt-header title="投诉主题" class="common-header">
					<mt-button slot="left" icon="back" @click="subjectVisible=false"></mt-button>
				</mt-header>
			</div>
			<div class="common-popup-content">
				<mt-radio
						v-model="subject"
						:options="subject_options">
				</mt-radio>
			</div>
		</mt-popup>
	</div>
		<div v-else>
			<!--投诉信息-->
			<div>
				<mt-cell class="menu-item" title="投诉信息" ></mt-cell>
				<div class="menu-content">
					<mt-cell title="投诉人" :value="complaint.accused_name"></mt-cell>
					<mt-cell title="投诉主题" :value="complaint.complain_subject_content"></mt-cell>
					<mt-cell title="投诉时间" :value="$moment.unix(complaint.complain_datetime).format('YYYY.MM.DD')"></mt-cell>
					<mt-cell title="投诉内容" :value="complaint.complain_content"></mt-cell>
					<div @click="isshow=true" v-if="complain_pic.length"><mt-cell title="投诉凭证" value="查看"></mt-cell></div>
				</div>
				<mt-popup v-model="isshow" popup-transition="popup-fade" v-if="complain_pic.length>0">
					<div class="preview-picture">
						<div
								class="picture-header"
								v-on:click="isshow=false"
						>
					<span>关闭</span
					><span v-if="complain_pic"
						>{{ defaultindex + 1 }} / {{ complain_pic.length }}</span
						>
						</div>

						<div class="picture-body">
							<mt-swipe
									:auto="0"
									:show-indicators="true"
									:default-index="defaultindex"
									class="ui-common-swiper"
									:prevent="false"
									:stop-propagation="true"
									@change="handleChange"
							>
								<mt-swipe-item
										v-for="(item, index) in complain_pic"
										v-bind:key="index"
								>
									<img v-bind:src="item" />
								</mt-swipe-item>

							</mt-swipe>
						</div>
					</div>
				</mt-popup>
			</div>
			<!--申诉信息-->
			<div v-if="complaint.complain_state>20">
				<mt-cell class="menu-item" title="申诉信息" ></mt-cell>
				<div class="menu-content">
					<mt-cell title="申诉时间" :value="$moment.unix(complaint.appeal_datetime).format('YYYY.MM.DD')"></mt-cell>
					<mt-cell title="申诉内容" :value="complaint.complain_appeal_content"></mt-cell>
					<div @click="isshow2=true" v-if="appeal_pic.length"><mt-cell title="申诉凭证" value="查看"></mt-cell></div>
				</div>
				<mt-popup v-model="isshow2" popup-transition="popup-fade" v-if="appeal_pic.length>0">
					<div class="preview-picture">
						<div
								class="picture-header"
								v-on:click="isshow2=false"
						>
					<span>关闭</span
					><span v-if="appeal_pic"
						>{{ defaultindex2 + 1 }} / {{ appeal_pic.length }}</span
						>
						</div>

						<div class="picture-body">
							<mt-swipe
									:auto="0"
									:show-indicators="true"
									:default-index="defaultindex2"
									class="ui-common-swiper"
									:prevent="false"
									:stop-propagation="true"
									@change="handleChange2"
							>
								<mt-swipe-item
										v-for="(item, index) in appeal_pic"
										v-bind:key="index"
								>
									<img v-bind:src="item" />
								</mt-swipe-item>

							</mt-swipe>
						</div>
					</div>
				</mt-popup>
			</div>
			<!--对话-->
			<div class="body">
				<mt-cell class="menu-item" title="对话记录" ></mt-cell>
				<mt-cell v-for="item in talk_list" :title="item.talk" ></mt-cell>
			</div>
			<div v-if="complaint.complain_state>20 && complaint.complain_state<99">
				<div class="body">
					<div class="order-comment-body">

						<div class="enter">
					<textarea
							placeholder="对话内容"
							v-model="content"
					></textarea>
						</div>
					</div>
				</div>
				<mt-button type="primary" @click='addComplaintTalk' class="ds-button-large">提交</mt-button>
			</div>
		</div>
	</div>
</template>

<script>
import { Toast } from 'mint-ui'
import { addComplaint, uploadComplaintPic, getCommonData, addComplaintTalk, getComplaintInfo, getComplaintTalk } from '../../../api/memberCompliant'

export default {
  data () {
    return {
      content: '',
      complain_id: this.$route.query.complain_id,
      order_id: this.$route.query.order_id,
      goods_id: this.$route.query.goods_id,
      subjectVisible: false,
      subject_options: [],
      subject_name: '',
      subject: '',
      image: ['', '', ''],
      file_value: ['', '', ''],
      complaint: {},
      complain_pic: [],
      appeal_pic: [],
      isshow: false,
      isshow2: false,
      defaultindex: 0,
      defaultindex2: 0,
      talk_list: []
    }
  },
  created () {
    if (!this.complain_id) {
      getCommonData(this.order_id, this.goods_id).then(res => {
        let subject_options = res.result.subject_list

        for (var i in subject_options) {
          this.subject_options.push({
            label: subject_options[i].complainsubject_content,
            value: subject_options[i].complainsubject_id + ',' + subject_options[i].complainsubject_content
          })
        }
        this.subject = subject_options[0].complainsubject_id + ',' + subject_options[0].complainsubject_content
      }).catch(function (error) {
        Toast(error.message)
        this.$router.go(-1)
      })
    } else {
      getComplaintTalk(this.complain_id).then(res => {
        this.talk_list = res.result.talk_list
      }).catch(function (error) {
        Toast(error.message)
      })
      getComplaintInfo(this.complain_id).then(res => {
        this.complaint = res.result.complain_info
        this.complain_pic = res.result.complain_pic
        this.appeal_pic = res.result.appeal_pic
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },
  watch: {
    subject: function (subject) {
      let temp = subject.split(',')
      this.subject_name = temp[1]
      this.subjectVisible = false
    }
  },
  methods: {
    handleChange (index) {
      this.defaultindex = index
    },
    handleChange2 (index) {
      this.defaultindex2 = index
    },
    addComplaintTalk () {
      addComplaintTalk(this.complain_id, this.content).then(res => {
        this.$router.go(0)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    addComplaint () {
      addComplaint(this.order_id, this.goods_id, this.subject, this.content, this.file_value).then(res => {
        this.$router.push({ name: 'MemberComplaintList' })
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    uploadComplaintPic (index, event) {
      if (typeof (event.target.files[0]) === 'undefined') {
        return
      }
      let formdata = new FormData()

      formdata.append('complain_pic', event.target.files[0])

      uploadComplaintPic(formdata).then(res => {
        this.image.splice(index, 1, res.result.pic + '?' + Math.floor(Math.random() * 100))
        this.file_value.splice(index, 1, res.result.file_name)
      }).catch(function (error) {
        Toast(error.message)
      })
    }

  }
}
</script>

<style lang="scss" scoped>
	.common-score-wrapper .back{display: block}
.container {
	display: flex;
	flex-direction: column;
	justify-content: flex-start;
	align-items: stretch;

	.body {
		top: 2.2rem;
		width: 100%;
		.order-comment-body {
			background: rgba(255, 255, 255, 1);
			padding:0.75rem;
			.body-list {
				display: flex;
				justify-content: left;
				align-content: center;
				align-items: center;
				padding-bottom:0.75rem;
				border-bottom: 1px solid #e8eaed;
			}
			.image {
				width:3.7rem;
				height:3.7rem;
				flex-shrink: 0;
				img {
					width: 100%;
					height: 100%;
				}
			}
			.comment {
				flex-basis: 100%;
				padding-left:0.75rem;
				span {
					font-size:0.8rem;
					color: #7c7f88;
					text-align: left;
					display: block;
				}
				ul {
					display: flex;
					justify-content: space-between;
					align-content: center;
					align-items: center;
					margin-top:1.2rem;
					li {
						img {
							width:1rem;
							height:1rem;
							flex-shrink: 0;
						}
						label {
							font-size:0.7rem;
							color: rgba(78, 84, 93, 1);
							font-weight: normal;
						}
					}
				}
			}
			.enter {
				padding-top:0.75rem;
				textarea {
					width: 100%;
					height: 6rem;
					background: rgba(247, 249, 250, 1);
					border: 1px solid #f7f9fa;
					box-sizing: border-box;
					padding: 0.5rem 0 0 0.5rem;
					font-size:0.7rem;
					-webkit-appearance: none;
					outline: none;
				}
			}
		}
	}
}
	.image-wrapper{display:flex}
	.user-avatar {
		flex:1;
		border:1px solid #eee;
		position: relative;
		width: 5rem;
		height: 5rem;
		margin: 0 auto;
		text-align: center;
		img {
			width: 100%;
			height: 100%
		}
		input {
			width: 100%;
			height: 100%;
			position: absolute;
			top: 0;
			left: 0;
			opacity: 0
		}
	}
	.user-avatar::before {
		position: absolute;
		font-size: 1.5rem;
		line-height: 5rem;
		left: 50%;
		margin-left: -.75rem;
		color: rgba(0, 0, 0, 0.5)
	}
	.swipe-wrapper {
		width: 100%;
	}
	.mint-popup {
		width: 100%;
		height: 100%;
		background-color: #000;
	}
	.mint-swipe,
	.mint-swipe-items-wrap {
		position: static;
	}
	.preview-picture {
		width: 100%;
		height: 100%;
		position: fixed;
		z-index: 10;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		background-color: #000;
		.picture-header {
			height:2.2rem;
			color: #000;
			background-color: #fff;
			display: flex;
			justify-content: center;
			align-content: center;
			align-items: center;
			width: 100%;
			top: 0;
			span {
				font-size:0.7rem;
				font-weight: normal;
				&:first-child {
					cursor: pointer;
					position: absolute;
					left:0.75rem;
					background-size:1.2rem;
					display: inline-block;
					height:2.2rem;
					line-height:2.2rem;
				}
			}
		}
		.picture-body {
			position: absolute;
			top:2.2rem;
			bottom: 0;
			width: 100%;
			display: flex;
			justify-content: center;
			align-content: center;
			align-items: center;
		}
	}
	.menu-item{background:#f7f9fa}

</style>
<style>
	.menu-content .mint-cell-title{flex:unset;width:4rem}
	.menu-content .mint-cell-value{flex:1}
</style>
