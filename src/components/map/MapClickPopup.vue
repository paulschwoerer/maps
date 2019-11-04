<template>
  <div class="map-click-popup">
    <template v-if="showFavoriteScreen">
      <h2 class="map-click-popup-title">{{ t("maps", "New Favorite") }}</h2>

      <div class="map-click-popup-content">
        <form
          @submit.prevent="handleNewFavoriteSubmit"
          class="new-favorite-form"
        >
          <div class="form-item">
            <span class="icon icon-add"></span>
            <input type="text" v-model="newFavoriteName" />
          </div>

          <div class="form-item">
            <span class="icon icon-category-organization"></span>
            <input type="text" v-model="newFavoriteCategory" />
          </div>

          <div class="buttons">
            <button>
              {{ t("maps", "Add") }}
            </button>
            <button @click.prevent="handleCancelAddingFavorite">
              {{ t("maps", "Cancel") }}
            </button>
          </div>
        </form>
      </div>
    </template>
    <template v-else>
      <h2 class="map-click-popup-title">{{ t("maps", "This Place") }}</h2>

      <div class="map-click-popup-content">
        <SimpleOSMAddress v-if="geocodeObject" :geocodeObject="geocodeObject" />
        <div v-else>
          <span>{{ t("maps", "Loading ...") }}</span>
        </div>

        <div class="spacing"></div>

        <button @click="handleAddToFavorites">
          {{ t("maps", "Add to Favorites") }}
        </button>
      </div>
    </template>
  </div>
</template>

<script>
import Actions from "@nextcloud/vue/dist/Components/Actions";
import ActionButton from "@nextcloud/vue/dist/Components/ActionButton";
import ActionInput from "@nextcloud/vue/dist/Components/ActionInput";
import { MAP_NAMESPACE } from "../../store/modules/map";
import { mapState } from "vuex";
import MapMode from "../../data/enum/MapMode";
import VueTypes from "vue-types";
import { geocode } from "../../utils/mapUtils";
import SimpleOSMAddress from "./SimpleOSMAddress";

export default {
  name: "MapClickPopup",

  props: {
    isVisible: VueTypes.bool.isRequired,
    coordinates: VueTypes.arrayOf(VueTypes.number).isRequired,

    addFavorite: VueTypes.func.isRequired
  },

  data() {
    return {
      geocodeObject: null,
      addingFavorite: false,
      newFavoriteName: "New Favorite",
      newFavoriteCategory: "Personal", // TODO: get default category name
      newFavoriteComment: ""
    };
  },

  watch: {
    isVisible(val) {
      if (!val) {
        this.addingFavorite = false;
        this.geocodeObject = null;
      }
    },
    coordinates: {
      deep: true,
      handler() {
        this.updateAddress();
      }
    }
  },

  computed: {
    ...mapState({
      mapMode: state => state[MAP_NAMESPACE].mode
    }),
    showFavoriteScreen() {
      return this.mapMode === MapMode.ADDING_FAVORITES || this.addingFavorite;
    }
  },

  methods: {
    handleCancelAddingFavorite() {
      if (this.mapMode === MapMode.ADDING_FAVORITES) {
        this.$emit("close");
      } else {
        this.addingFavorite = false;
      }
    },

    handleNewFavoriteSubmit() {
      const [lat, lng] = this.coordinates;

      const promise = this.addFavorite({
        lat,
        lng,
        name: this.newFavoriteName,
        category: this.newFavoriteCategory,
        description: ""
      });

      if (promise instanceof Promise) {
        promise.then(() => {
          this.$emit("close");
        });
      }
    },
    handleAddToFavorites() {
      this.addingFavorite = true;
    },
    updateAddress() {
      const [lat, lng] = this.coordinates;

      geocode(`${lat},${lng}`).then(res => {
        this.geocodeObject = res;
      });
    }
  },

  components: {
    ActionButton,
    Actions,
    ActionInput,
    SimpleOSMAddress
  }
};
</script>

<style scoped lang="scss">
.map-click-popup {
  width: 180px;

  .map-click-popup-title {
    color: #333;
    text-transform: uppercase;
    font-size: 1em;
    margin: 0;
    white-space: nowrap;
  }

  .map-click-popup-content {
    padding: 0.5em 0;

    .spacing {
      padding: 0.5em 0;
    }

    .new-favorite-form {
      width: 100%;
      padding: 0;
      margin: 0;

      .form-item {
        display: flex;
        align-items: center;

        .icon {
          display: flex;
          flex-grow: 0;
          flex-shrink: 0;
          justify-content: center;
          align-items: center;
          width: 44px;
          height: 44px;
        }
      }
    }
  }
}
</style>
