package cat.fansubs.app.serveraccess;

import cat.fansubs.app.serveraccess.model.PiwigoCategoriesResponse;
import cat.fansubs.app.serveraccess.model.PiwigoImagesResponse;
import cat.fansubs.app.serveraccess.model.base.PiwigoResponse;
import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Query;

public interface PiwigoService {
    @GET("ws.php?format=json&method=pwg.categories.getList")
    Call<PiwigoResponse<PiwigoCategoriesResponse>> getCategories(@Query("cat_id") Long categoryId);

    @GET("ws.php?format=json&method=pwg.categories.getImages&per_page=500")
    Call<PiwigoResponse<PiwigoImagesResponse>> getImages(@Query("cat_id") Long categoryId);
}
