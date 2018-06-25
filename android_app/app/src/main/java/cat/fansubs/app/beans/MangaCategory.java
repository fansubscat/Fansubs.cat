package cat.fansubs.app.beans;

public class MangaCategory {
    private Long id;
    private String name;
    private String comment;
    private String tnUrl;
    private Long totalNbImages;
    private Long nbCategories;

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getComment() {
        return comment;
    }

    public void setComment(String comment) {
        this.comment = comment;
    }

    public String getTnUrl() {
        return tnUrl;
    }

    public void setTnUrl(String tnUrl) {
        this.tnUrl = tnUrl;
    }

    public Long getTotalNbImages() {
        return totalNbImages;
    }

    public void setTotalNbImages(Long totalNbImages) {
        this.totalNbImages = totalNbImages;
    }

    public Long getNbCategories() {
        return nbCategories;
    }

    public void setNbCategories(Long nbCategories) {
        this.nbCategories = nbCategories;
    }
}
