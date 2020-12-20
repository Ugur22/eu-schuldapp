<template>
  <nb-container>
   <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent >
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Mijn schulden</nb-title>
      </nb-body>
      <nb-right>
        <nb-button transparent>
          <nb-icon name="info" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content>
      <nb-item :style="{ borderColor: '#62B1F6' }">
        <nb-input placeholder="Search" />
      </nb-item>
      <nb-list>
        <nb-list-item>
          <nb-left>
            <nb-button transparent :on-press="() => detailDebt(1)">
              <nb-text>ING</nb-text>
            </nb-button>
          </nb-left>
          <nb-body>
            <nb-text>$310,000</nb-text>
          </nb-body>
          <nb-right>
            <nb-icon name="arrow-forward" />
          </nb-right>
        </nb-list-item>
        <nb-list-item>
          <nb-left>
            <nb-button transparent :on-press="() => detailDebt(1)">
              <nb-text>Belastingdienst</nb-text>
            </nb-button>
          </nb-left>
          <nb-body>
            <nb-text>$310,000</nb-text>
          </nb-body>
          <nb-right>
            <nb-icon name="arrow-forward" />
          </nb-right>
        </nb-list-item>
        <nb-list-item>
          <nb-left>
            <nb-button transparent :on-press="() => detailDebt(1)">
              <nb-text>Hypotheek</nb-text>
            </nb-button>
          </nb-left>
          <nb-body>
            <nb-text>$310,000</nb-text>
          </nb-body>
          <nb-right>
            <nb-icon name="arrow-forward" />
          </nb-right>
        </nb-list-item>
      </nb-list>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="docs"></footer-nav>
    </nb-footer>
    <modal v-if="isModalVisible">
      <nb-header>
      <nb-left>
        <nb-button transparent >
          <nb-icon name="arrow-back" :on-press="dismissModal" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Details</nb-title>
      </nb-body>
      <nb-right />
    </nb-header>
    <nb-content>
      <nb-card :style="{ marginLeft: 20, marginRight: 20, marginTop: 20, marginBottom: 20 }">
        <nb-card-item rounded :style="{ backgroundColor: '#62B1F6' }">
          <nb-body>
            <nb-grid class="marginBottom">
              <nb-col>
                  <nb-text class="headerText">Kenmerk Ref</nb-text>
                  <nb-text class="detailText">12345678</nb-text>
              </nb-col>
              <nb-col>
                  <nb-text class="headerText">Percentage</nb-text>
                  <nb-text class="detailText">12345678</nb-text>
              </nb-col>
            </nb-grid>
            <nb-grid class="marginBottom">
              <nb-col>
                  <nb-text class="headerText">Incasseerder</nb-text>
                  <nb-text class="detailText">12345678</nb-text>
              </nb-col>
              <nb-col>
                  <nb-text class="headerText">Schuld</nb-text>
                  <nb-text class="detailText">$585.89</nb-text>
              </nb-col>
            </nb-grid>
          </nb-body>            
        </nb-card-item>
      </nb-card>
    </nb-content>
    </modal>
  </nb-container>
</template>

<style>
  .headerText {
    color: white; 
    font-weight: bold;
  }
  .detailText {
    color: white;
  }
  .marginBottom {
    margin-bottom: 20px
  }
</style>

<script>
  import Modal from 'react-native-modal';
  import FooterNav from '../included/Footer';

  export default {
    props: {
      navigation: {
        type: Object
      }
    },
    components: { FooterNav },
    data() {
      return {
        isModalVisible: false
      };
    },
    methods: {
      goBack: function () {
        this.navigation.goBack();
      },
      goToPage: function (page) {
        this.navigation.navigate(page);
      },
      detailDebt: function (id) {
        this.isModalVisible = true;
      },
      dismissModal: function () {
        this.isModalVisible = false;
      }
    }
  }
</script>
