<?xml version="1.0" encoding="ISO-8859-1"?>
<!-- 
 @package concerto.docs
 
 @copyright Copyright &copy; 2005, Middlebury College
 @license http://www.gnu.org/copyleft/gpl.html GNU General Public License (GPL)
 
 @version $Id$
 -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="trim.xsl"/>
<xsl:import href="paragraphs.xsl"/>
<xsl:output format="text" />
<xsl:strip-space elements="fix change new important" />
<!--
///////////////////////////////////////////////////////////////////////
// changelog
///////////////////////////////////////////////////////////////////////
-->
<xsl:template match="changelog">
Name: <xsl:value-of select="@name" /> Change Log
<xsl:text>

</xsl:text>
	<xsl:for-each select="version">
v. <xsl:value-of select="@number" /><xsl:if test="@date!=''"> (<xsl:value-of select="@date" />)</xsl:if>
----------------------------------------------------
<xsl:call-template name="addNewlines">
	<xsl:with-param name="maxCharacters" select="84"/>
	<xsl:with-param name="remainingString">
		<xsl:call-template name="singleLineParagraphs">
			<xsl:with-param name="s" select="releaseNotes"/>
		</xsl:call-template>		
	</xsl:with-param>
</xsl:call-template>

<xsl:apply-templates />

	</xsl:for-each>
</xsl:template>

<!--
///////////////////////////////////////////////////////////////////////
// fix
///////////////////////////////////////////////////////////////////////
-->
<xsl:template match="fix">
	* Bug Fix: <xsl:call-template name="entry" />
</xsl:template>

<!--
///////////////////////////////////////////////////////////////////////
// change
///////////////////////////////////////////////////////////////////////
-->
<xsl:template match="change">
	* Change: <xsl:call-template name="entry" />
</xsl:template>

<!--
///////////////////////////////////////////////////////////////////////
// new
///////////////////////////////////////////////////////////////////////
-->
<xsl:template match="new">
	* New feature: <xsl:call-template name="entry" />
</xsl:template>

<!--
///////////////////////////////////////////////////////////////////////
// important
///////////////////////////////////////////////////////////////////////
-->
<xsl:template match="important">
	**** IMPORTANT *** Change: <xsl:call-template name="entry" />
</xsl:template>

<!--
///////////////////////////////////////////////////////////////////////
// entry
///////////////////////////////////////////////////////////////////////
-->
<xsl:template name="entry">
	<xsl:if test="@ref">#<xsl:value-of select="@ref" /><xsl:text> </xsl:text></xsl:if>
	<xsl:text>&#x0A;&#x09;&#x09;</xsl:text>
	<xsl:call-template name="addNewlines">
		<xsl:with-param name="maxCharacters" select="76"/>
		<xsl:with-param name="tabs" select="'&#x09;&#x09;'"/>
		<xsl:with-param name="remainingString">
			<xsl:call-template name="singleLineParagraphs">
				<xsl:with-param name="s" select="."/>
			</xsl:call-template>		
		</xsl:with-param>
	</xsl:call-template>
	
	<xsl:if test="@author">
		<xsl:text>&#x0A;&#x09;&#x09;</xsl:text>
		<xsl:text>(</xsl:text>
		
		<xsl:if test="@date!=''"><xsl:value-of select="@date" /> - </xsl:if>
		
		<xsl:call-template name="authors">
			<xsl:with-param name="str" select="@author"/>
		</xsl:call-template>
				
		<xsl:text>)</xsl:text>
	</xsl:if>
	<xsl:if test="@reporter">
		<xsl:text>&#x0A;&#x09;&#x09;</xsl:text>
		<xsl:text>(reported by </xsl:text>		
		<xsl:call-template name="reporters">
			<xsl:with-param name="str" select="@reporter"/>
		</xsl:call-template>
				
		<xsl:text>)</xsl:text>
	</xsl:if>
</xsl:template>

<xsl:template name="authors">
	<xsl:param name="str"/>
	<xsl:choose>
		<xsl:when test="contains($str,',')">
			<xsl:call-template name="authors">
				<xsl:with-param name="str" select="substring-before($str,',')"/>
			</xsl:call-template>			
			<xsl:text>, </xsl:text>
			<xsl:call-template name="authors">
				<xsl:with-param name="str" select="substring-after($str,',')"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:variable name="author">
				<xsl:call-template name="trim">
					<xsl:with-param name="s" select="$str"/>
				</xsl:call-template>
			</xsl:variable>
		
			<xsl:choose>
				<xsl:when test="//authors/name[@short=$author]">
					<xsl:value-of select="//authors/name[@short=$author]" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$author" />
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="reporters">
	<xsl:param name="str"/>
	
	<xsl:choose>
	<xsl:when test="contains($str,',')">
		<xsl:call-template name="reporters">
			<xsl:with-param name="str" select="substring-before($str,',')"/>
		</xsl:call-template>
		<xsl:text>, </xsl:text>
		<xsl:call-template name="reporters">
			<xsl:with-param name="str" select="substring-after($str,',')"/>
		</xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<xsl:variable name="reporter">
			<xsl:call-template name="trim">
				<xsl:with-param name="s" select="$str"/>
			</xsl:call-template>
		</xsl:variable>
	
		<xsl:choose>
			<xsl:when test="//reporters/reporter[@short=$reporter]">
				<xsl:value-of select="//reporters/reporter[@short=$reporter]/name" />
				<xsl:choose>
					<xsl:when test="//reporters/reporter[@short=$reporter]/institution">
						 <xsl:text> of </xsl:text>
						<xsl:value-of select="//reporters/reporter[@short=$reporter]/institution" />
					</xsl:when>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$reporter" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<!-- Ignore the releaseNotes data as we've already used it. -->
<xsl:template match="releaseNotes"></xsl:template>

</xsl:stylesheet>
