<details class="spoiler">
	<summary class="spoiler-header">
		<span class="spoiler-title">
			<xsl:choose>
				<xsl:when test="@title and string-length(normalize-space(@title)) > 0">
					<xsl:choose>
						<xsl:when test="string-length(normalize-space(@title)) > 100">
							<xsl:value-of select="concat(normalize-space(substring(normalize-space(@title), 0, 100)), '…')"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="normalize-space(@title)"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>{L_SPOILER}</xsl:otherwise>
			</xsl:choose>
			<small><em> (prem per a mostrar-lo o amagar-lo)</em></small>
		</span>
		<span class="spoiler-status">
			<i class="icon fa-fw fa-eye" aria-hidden="true"></i>
		</span>
	</summary>
	<div class="spoiler-body"><xsl:apply-templates/></div>
</details>
