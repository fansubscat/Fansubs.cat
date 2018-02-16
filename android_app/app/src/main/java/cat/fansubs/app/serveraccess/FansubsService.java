package cat.fansubs.app.serveraccess;

import java.util.List;

import cat.fansubs.app.beans.Fansub;
import cat.fansubs.app.beans.News;
import cat.fansubs.app.serveraccess.model.base.ServerResponse;
import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Query;

public interface FansubsService {
    @GET("news")
    Call<ServerResponse<News>> getNews(@Query("page") int page, @Query("search") String search, @Query("fansub_ids[]") List<String> fansubIds);

    @GET("fansubs")
    Call<ServerResponse<Fansub>> getFansubs();
}
