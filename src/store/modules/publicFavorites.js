import AppMode from "../../data/enum/MapMode";
import { publicApiRequest, showNotification } from "../../utils/common";
import { getCategoryRawName } from "../../utils/mapUtils";

export const PUBLIC_FAVORITES_NAMESPACE = "publicFavorites";

const state = {
  favorites: [],
  appMode: AppMode.DEFAULT
};

const getters = {
  mappedByCategory(state) {
    if (state.favorites.length === 0) {
      return {};
    }

    return {
      [getCategoryRawName(state.favorites[0].category)]: state.favorites
    };
  }
};

const actions = {
  getFavorites({ commit }) {
    return publicApiRequest("favorites", "GET")
      .then(data => {
        commit("setFavorites", data);
      })
      .catch(() => showNotification(t("maps", "Failed to get favorites")));
  },
  addFavorite({ commit }, { lat, lng, name, category }) {
    return publicApiRequest("favorites", "POST", {
      lat,
      lng,
      name,
      category,
      comment: "",
      extensions: ""
    })
      .then(data => {
        commit("addFavorite", data);
      })
      .catch(() => showNotification(t("maps", "Failed to create favorite")));
  }
};

const mutations = {
  setFavorites(state, favorites) {
    state.favorites = favorites;
  },
  addFavorite(state, favorite) {
    state.favorites.push(favorite);
  },
  setAppMode(state, mode) {
    state.appMode = mode;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
